<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Beverage;
use App\Models\Guest;
use App\Models\GuestPreference;
use App\Models\Notification;
use App\Models\User;
use App\Mail\GuestConfirmationNotification;
use App\Services\WhatsApp\UltraMsgService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InvitationController extends Controller
{
    public function show(string $token): View
    {
        $guest = Guest::with(['table', 'preferences.beverage'])
            ->where('invitation_token', $token)
            ->firstOrFail();

        $data = $this->buildInvitationData($guest, true);

        return view('invitations.invitation', $data);
    }

    public function download(string $token)
    {
        $guest = Guest::with('table')
            ->where('invitation_token', $token)
            ->firstOrFail();

        $data = $this->buildInvitationData($guest, false);

        Pdf::setOptions([
            'isRemoteEnabled' => true,
        ]);

        $pdf = Pdf::loadView('invitations.pdf', [
            'guest' => $guest,
            'event' => $data['event'],
            'invitationUrl' => $data['invitationUrl'],
            'qrCodeDataUri' => $data['qrCodeDataUri'],
            'backgroundImage' => $data['pdfAssets']['background'] ?? null,
            'bouquetImage' => $data['pdfAssets']['bouquet'] ?? null,
        ])->setPaper('a4', 'portrait');

        // Utiliser le nom de l'invité pour le nom du fichier
        $guestName = Str::slug($guest->display_name, '-');
        $filename = 'Invitation-' . $guestName . '.pdf';

        return $pdf->download($filename);
    }

    public function downloadImage(string $token): BinaryFileResponse
    {
        $guest = Guest::with('table')
            ->where('invitation_token', $token)
            ->firstOrFail();

        $data = $this->buildInvitationData($guest, false);

        Pdf::setOptions([
            'isRemoteEnabled' => true,
        ]);

        $pdf = Pdf::loadView('invitations.pdf', [
            'guest' => $guest,
            'event' => $data['event'],
            'invitationUrl' => $data['invitationUrl'],
            'qrCodeDataUri' => $data['qrCodeDataUri'],
            'backgroundImage' => $data['pdfAssets']['background'] ?? null,
            'bouquetImage' => $data['pdfAssets']['bouquet'] ?? null,
        ])->setPaper('a4', 'portrait');

        // Générer un nom de fichier unique pour le PDF temporaire
        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempPdfPath = $tempDir . '/' . uniqid('invitation_', true) . '.pdf';
        // pdftoppm ajoute automatiquement -1.png, donc on ne met pas l'extension
        $tempImageBase = $tempDir . '/' . uniqid('invitation_', true);

        try {
            // Sauvegarder le PDF temporairement
            $pdfContent = $pdf->output();
            if (empty($pdfContent)) {
                throw new \RuntimeException('Le PDF généré est vide.');
            }

            $written = file_put_contents($tempPdfPath, $pdfContent);
            if ($written === false || !file_exists($tempPdfPath)) {
                throw new \RuntimeException('Impossible de sauvegarder le PDF temporaire.');
            }

            // Vérifier que le fichier PDF est valide (taille > 0)
            if (filesize($tempPdfPath) === 0) {
                throw new \RuntimeException('Le fichier PDF généré est vide.');
            }

            // Convertir uniquement la première page du PDF en image avec ImageMagick convert
            // [0] signifie la première page du PDF
            // -density 300 : résolution 300 DPI pour une bonne qualité
            // -quality 95 : qualité de compression PNG
            $finalImagePath = $tempImageBase . '.png';
            $command = sprintf(
                'convert -density 300 -quality 95 %s[0] %s 2>&1',
                escapeshellarg($tempPdfPath),
                escapeshellarg($finalImagePath)
            );

            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                $errorMsg = !empty($output) ? implode("\n", $output) : "Code de retour: $returnCode";
                throw new \RuntimeException("Échec de la conversion PDF en image (ImageMagick): $errorMsg");
            }

            if (!file_exists($finalImagePath)) {
                $errorMsg = !empty($output) ? implode("\n", $output) : "Fichier généré introuvable";
                throw new \RuntimeException("Le fichier image n'a pas été généré: $errorMsg");
            }

            // Vérifier que l'image générée n'est pas vide
            if (filesize($finalImagePath) === 0) {
                throw new \RuntimeException('Le fichier image généré est vide.');
            }

            // Renommer le fichier généré pour le téléchargement
            $guestName = Str::slug($guest->display_name, '-');
            $downloadImagePath = $tempDir . '/Invitation-' . $guestName . '.png';
            rename($finalImagePath, $downloadImagePath);
            $finalImagePath = $downloadImagePath;

            // Nettoyer le PDF temporaire
            if (file_exists($tempPdfPath)) {
                unlink($tempPdfPath);
            }

            // Retourner l'image en téléchargement
            return response()->download($finalImagePath, 'Invitation-' . $guestName . '.png', [
                'Content-Type' => 'image/png',
            ])->deleteFileAfterSend(true);

        } catch (\Throwable $e) {
            // Nettoyer les fichiers temporaires en cas d'erreur
            if (file_exists($tempPdfPath)) {
                unlink($tempPdfPath);
            }
            if (file_exists($tempImageBase . '.png')) {
                unlink($tempImageBase . '.png');
            }
            if (isset($finalImagePath) && file_exists($finalImagePath) && $finalImagePath !== $tempImageBase . '.png') {
                @unlink($finalImagePath);
            }

            report($e);
            abort(500, 'Impossible de générer l\'image de l\'invitation. Veuillez réessayer.');
        }
    }

    public function sendLinkViaWhatsApp(Request $request, string $token, UltraMsgService $whatsAppService): RedirectResponse|JsonResponse
    {
        try {
            $guest = Guest::where('invitation_token', $token)->firstOrFail();

            if (! $guest->phone) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Le numéro de téléphone de l\'invité n\'est pas renseigné.',
                    ], 400);
                }

                return redirect()->route('invitations.show', $token)
                    ->with('error', 'Le numéro de téléphone de l\'invité n\'est pas renseigné.');
            }

            $result = $whatsAppService->sendInvitation($guest);

            if ($result['sent']) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'status' => 'ok',
                        'message' => 'Lien d\'invitation envoyé avec succès via WhatsApp.',
                    ]);
                }

                return redirect()->route('invitations.show', $token)
                    ->with('status', 'Lien d\'invitation envoyé avec succès via WhatsApp à ' . $guest->display_name . '.');
            } else {
                $errorMessage = $result['response']['error'] ?? 'Erreur inconnue lors de l\'envoi du lien.';

                if ($request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Échec de l\'envoi: ' . $errorMessage,
                    ], 500);
                }

                return redirect()->route('invitations.show', $token)
                    ->with('error', 'Échec de l\'envoi du lien à ' . $guest->display_name . ': ' . $errorMessage);
            }
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ], 400);
            }

            return redirect()->route('invitations.show', $token)
                ->with('error', 'Erreur: ' . $e->getMessage());
        } catch (\RuntimeException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Configuration WhatsApp manquante: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->route('invitations.show', $token)
                ->with('error', 'Configuration WhatsApp manquante: ' . $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Une erreur inattendue est survenue lors de l\'envoi du lien.',
                ], 500);
            }

            return redirect()->route('invitations.show', $token)
                ->with('error', 'Une erreur inattendue est survenue lors de l\'envoi du lien WhatsApp.');
        }
    }

    public function sendPdfViaWhatsApp(Request $request, string $token, UltraMsgService $whatsAppService): RedirectResponse|JsonResponse
    {
        try {
            $guest = Guest::with('table')
                ->where('invitation_token', $token)
                ->firstOrFail();

            if (! $guest->phone) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Le numéro de téléphone de l\'invité n\'est pas renseigné.',
                    ], 400);
                }

                return redirect()->route('invitations.show', $token)
                    ->with('error', 'Le numéro de téléphone de l\'invité n\'est pas renseigné.');
            }

            // Générer le PDF
            $data = $this->buildInvitationData($guest, false);

            Pdf::setOptions([
                'isRemoteEnabled' => true,
            ]);

            $pdf = Pdf::loadView('invitations.pdf', [
                'guest' => $guest,
                'event' => $data['event'],
                'invitationUrl' => $data['invitationUrl'],
                'qrCodeDataUri' => $data['qrCodeDataUri'],
                'backgroundImage' => $data['pdfAssets']['background'] ?? null,
                'bouquetImage' => $data['pdfAssets']['bouquet'] ?? null,
            ])->setPaper('a4', 'portrait');

            // Sauvegarder temporairement le PDF
            $tempDir = storage_path('app/temp');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $filename = 'invitation_' . $guest->invitation_token . '_' . time() . '.pdf';
            $tempPath = $tempDir . '/' . $filename;

            $pdf->save($tempPath);

            try {
                // Envoyer le PDF via WhatsApp
                $result = $whatsAppService->sendInvitationPdf($guest, $tempPath);

                // Supprimer le fichier temporaire après l'envoi
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }

                if ($result['sent']) {
                    if ($request->wantsJson()) {
                        return response()->json([
                            'status' => 'ok',
                            'message' => 'PDF de l\'invitation envoyé avec succès via WhatsApp.',
                        ]);
                    }

                    return redirect()->route('invitations.show', $token)
                        ->with('status', 'PDF de l\'invitation envoyé avec succès via WhatsApp à ' . $guest->display_name . '.');
                } else {
                    $errorMessage = $result['response']['error'] ?? 'Erreur inconnue lors de l\'envoi du PDF.';

                    if ($request->wantsJson()) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Échec de l\'envoi: ' . $errorMessage,
                        ], 500);
                    }

                    return redirect()->route('invitations.show', $token)
                        ->with('error', 'Échec de l\'envoi du PDF à ' . $guest->display_name . ': ' . $errorMessage);
                }
            } catch (\Throwable $e) {
                // Supprimer le fichier temporaire en cas d'erreur
                if (file_exists($tempPath)) {
                    @unlink($tempPath);
                }
                throw $e;
            }
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ], 400);
            }

            return redirect()->route('invitations.show', $token)
                ->with('error', 'Erreur: ' . $e->getMessage());
        } catch (\RuntimeException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Configuration WhatsApp manquante: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->route('invitations.show', $token)
                ->with('error', 'Configuration WhatsApp manquante: ' . $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Une erreur inattendue est survenue lors de l\'envoi du PDF.',
                ], 500);
            }

            return redirect()->route('invitations.show', $token)
                ->with('error', 'Une erreur inattendue est survenue lors de l\'envoi du PDF WhatsApp.');
        }
    }

    public function sendImageViaWhatsApp(Request $request, string $token, UltraMsgService $whatsAppService): RedirectResponse|JsonResponse
    {
        try {
            $guest = Guest::with('table')
                ->where('invitation_token', $token)
                ->firstOrFail();

            if (! $guest->phone) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Le numéro de téléphone de l\'invité n\'est pas renseigné.',
                    ], 400);
                }

                return redirect()->route('invitations.show', $token)
                    ->with('error', 'Le numéro de téléphone de l\'invité n\'est pas renseigné.');
            }

            // Générer l'image de l'invitation
            $data = $this->buildInvitationData($guest, false);

            Pdf::setOptions([
                'isRemoteEnabled' => true,
            ]);

            $pdf = Pdf::loadView('invitations.pdf', [
                'guest' => $guest,
                'event' => $data['event'],
                'invitationUrl' => $data['invitationUrl'],
                'qrCodeDataUri' => $data['qrCodeDataUri'],
                'backgroundImage' => $data['pdfAssets']['background'] ?? null,
                'bouquetImage' => $data['pdfAssets']['bouquet'] ?? null,
            ])->setPaper('a4', 'portrait');

            // Générer un nom de fichier unique pour le PDF temporaire
            $tempDir = storage_path('app/temp');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $tempPdfPath = $tempDir . '/' . uniqid('invitation_', true) . '.pdf';
            $tempImageBase = $tempDir . '/' . uniqid('invitation_', true);
            $tempImagePath = $tempImageBase . '.png';

            try {
                // Sauvegarder le PDF temporairement
                $pdfContent = $pdf->output();
                if (empty($pdfContent)) {
                    throw new \RuntimeException('Le PDF généré est vide.');
                }

                $written = file_put_contents($tempPdfPath, $pdfContent);
                if ($written === false || !file_exists($tempPdfPath)) {
                    throw new \RuntimeException('Impossible de sauvegarder le PDF temporaire.');
                }

                if (filesize($tempPdfPath) === 0) {
                    throw new \RuntimeException('Le fichier PDF généré est vide.');
                }

                // Convertir uniquement la première page du PDF en image avec ImageMagick convert
                // [0] signifie la première page du PDF
                // -density 300 : résolution 300 DPI pour une bonne qualité
                // -quality 95 : qualité de compression PNG
                $command = sprintf(
                    'convert -density 300 -quality 95 %s[0] %s 2>&1',
                    escapeshellarg($tempPdfPath),
                    escapeshellarg($tempImagePath)
                );

                $output = [];
                $returnCode = 0;
                exec($command, $output, $returnCode);

                if ($returnCode !== 0) {
                    $errorMsg = !empty($output) ? implode("\n", $output) : "Code de retour: $returnCode";
                    throw new \RuntimeException("Échec de la conversion PDF en image (ImageMagick): $errorMsg");
                }

                if (!file_exists($tempImagePath)) {
                    $errorMsg = !empty($output) ? implode("\n", $output) : "Fichier généré introuvable";
                    throw new \RuntimeException("Le fichier image n'a pas été généré: $errorMsg");
                }

                // Vérifier que l'image générée n'est pas vide
                if (filesize($tempImagePath) === 0) {
                    throw new \RuntimeException('Le fichier image généré est vide.');
                }

                // Envoyer l'image via WhatsApp
                $result = $whatsAppService->sendInvitationImage($guest, $tempImagePath);

                // Nettoyer les fichiers temporaires
                if (file_exists($tempPdfPath)) {
                    unlink($tempPdfPath);
                }
                if (file_exists($tempImagePath)) {
                    unlink($tempImagePath);
                }

                if ($result['sent']) {
                    if ($request->wantsJson()) {
                        return response()->json([
                            'status' => 'ok',
                            'message' => 'Image de l\'invitation envoyée avec succès via WhatsApp.',
                        ]);
                    }

                    return redirect()->route('invitations.show', $token)
                        ->with('status', 'Image de l\'invitation envoyée avec succès via WhatsApp à ' . $guest->display_name . '.');
                } else {
                    $errorMessage = $result['response']['error'] ?? 'Erreur inconnue lors de l\'envoi de l\'image.';

                    if ($request->wantsJson()) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Échec de l\'envoi: ' . $errorMessage,
                        ], 500);
                    }

                    return redirect()->route('invitations.show', $token)
                        ->with('error', 'Échec de l\'envoi de l\'image à ' . $guest->display_name . ': ' . $errorMessage);
                }
            } catch (\Throwable $e) {
                // Nettoyer les fichiers temporaires en cas d'erreur
                if (file_exists($tempPdfPath)) {
                    @unlink($tempPdfPath);
                }
                if (file_exists($tempImagePath)) {
                    @unlink($tempImagePath);
                }
                throw $e;
            }
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ], 400);
            }

            return redirect()->route('invitations.show', $token)
                ->with('error', 'Erreur: ' . $e->getMessage());
        } catch (\RuntimeException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Configuration WhatsApp manquante: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->route('invitations.show', $token)
                ->with('error', 'Configuration WhatsApp manquante: ' . $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Une erreur inattendue est survenue lors de l\'envoi de l\'image.',
                ], 500);
            }

            return redirect()->route('invitations.show', $token)
                ->with('error', 'Une erreur inattendue est survenue lors de l\'envoi de l\'image WhatsApp.');
        }
    }

    public function confirm(Request $request, string $token): RedirectResponse|JsonResponse
    {
        $guest = Guest::where('invitation_token', $token)->firstOrFail();
        $preferencesMessage = null;

        if ($guest->rsvp_status !== 'confirmed') {
            $guest->forceFill([
                'rsvp_status' => 'confirmed',
                'rsvp_confirmed_at' => now(),
            ])->save();

            $this->storePreferences($guest, $request->input('beverage_ids', []));
            $preferencesMessage = 'Vos préférences ont été enregistrées.';

            // Créer une notification pour tous les utilisateurs
            $this->createNotificationsForUsers($guest);

            // Envoyer un email à tous les utilisateurs
            $this->sendEmailNotifications($guest);
        } else {
            $preferencesMessage = 'Vos préférences étaient déjà enregistrées.';
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Merci, votre présence est confirmée.',
                'preferences_message' => $preferencesMessage,
            ]);
        }

        return redirect()->route('invitations.show', $token)
            ->with('status', 'Merci, votre présence est confirmée.')
            ->with('preferences_status', $preferencesMessage);
    }

    public function updatePreferences(Request $request, string $token): RedirectResponse|JsonResponse
    {
        $guest = Guest::where('invitation_token', $token)->firstOrFail();

        if ($guest->rsvp_status === 'confirmed') {
            return redirect()->route('invitations.show', $token)
                ->with('preferences_status', 'Vous avez déjà confirmé votre présence.');
        }

        $this->storePreferences($guest, $request->input('beverage_ids', []));

        $message = 'Merci, vos préférences ont été enregistrées.';

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'ok',
                'message' => $message,
            ]);
        }

        return redirect()->route('invitations.show', $token)->with('preferences_status', $message);
    }

    public function buildInvitationData(Guest $guest, bool $withBeverages = true): array
    {
        $invitationUrl = route('invitations.show', $guest->invitation_token);
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=380x380&data=' . urlencode($invitationUrl);
        $qrCodeDataUri = null;
        $downloadNotice = null;

        try {
            $response = Http::timeout(5)->withoutVerifying()->withHeaders([
                'Accept' => 'image/png',
            ])->get($qrCodeUrl);
            if ($response->successful()) {
                $qrCodeDataUri = 'data:image/png;base64,' . base64_encode($response->body());
            }
        } catch (\Throwable $exception) {
            report($exception);
            $downloadNotice = "Le QR code n'a pas pu être récupéré. Vérifiez votre connexion avant de télécharger.";
        }

        if (! $qrCodeDataUri) {
            $qrCodeDataUri = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8+B8AAwMCAO4P8LkAAAAASUVORK5CYII=';
            $downloadNotice = $downloadNotice ?? "Le téléchargement fonctionne avec un QR générique. Connectez-vous à Internet pour un QR personnalisé.";
        }

        $event = [
            'page_title' => 'Mariage Raphael et Daniella',
            'couple_names' => 'Raphael et Daniella',
            'date_long' => 'Samedi 29 novembre 2025',
            'ceremony_time' => '10h00',
            'ceremony_title' => 'Bénédiction Nuptiale',
            'ceremony_location' => "Église La Borne Cité verte\n12e rue\nRéf: ex Promedis ou N6",
            'ceremony_map_query' => 'Église La Borne Cité Verte 12e Rue Kinshasa',
            'reception_time' => '19h00',
            'reception_title' => 'Soirée dansante',
            'reception_location' => "Salle Malaïka\nC/ Ngaliema, route de Matadi, Q/ Météo\nRéf: Regideso",
            'reception_map_query' => 'Salle Malaïka Ngaliema Kinshasa',
            'dress_code' => 'All black',
        ];
        $event['pdf_filename'] = 'Invitation-' . Str::slug($event['couple_names'] ?? 'mariage', '-') . '.pdf';

        // Nom du fichier PDF basé sur le nom de l'invité
        $guestPdfFilename = 'Invitation-' . Str::slug($guest->display_name, '-') . '.pdf';

        $pdfAssets = [
            'background' => $this->encodePublicAsset('invitations/fond.jpeg'),
            'bouquet' => $this->encodePublicAsset('invitations/bouquet.png'),
        ];
        $pdfAssetUrls = [
            'background' => asset('invitations/fond.jpeg'),
            'bouquet' => asset('invitations/bouquet.png'),
        ];

        $beverages = collect();
        $beverageMap = collect();

        if ($withBeverages) {
            $allBeverages = Beverage::orderBy('name')->get();
            $beverages = $allBeverages->groupBy('category')->map(fn ($group) => $group->values());
            $beverageMap = $allBeverages->keyBy('id')->map->name;
        }

        return [
            'guest' => $guest,
            'event' => $event,
            'invitationUrl' => $invitationUrl,
            'qrCodeUrl' => $qrCodeUrl,
            'qrCodeDataUri' => $qrCodeDataUri,
            'beverages' => $beverages,
            'beverageMap' => $beverageMap,
            'downloadNotice' => $downloadNotice,
            'pdfAssets' => $pdfAssets,
            'pdfAssetUrls' => $pdfAssetUrls,
            'guestPdfFilename' => $guestPdfFilename,
        ];
    }

    protected function storePreferences(Guest $guest, $beverageIds): void
    {
        if (! is_array($beverageIds)) {
            $beverageIds = [$beverageIds];
        }

        $beverageIds = collect($beverageIds)
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->take(2)
            ->values();

        $validIds = Beverage::whereIn('id', $beverageIds)->pluck('id')->values();

        DB::transaction(function () use ($guest, $validIds) {
            GuestPreference::where('guest_id', $guest->id)->delete();

            foreach ($validIds as $id) {
                GuestPreference::create([
                    'guest_id' => $guest->id,
                    'beverage_id' => $id,
                ]);
            }
        });
    }

    protected function encodePublicAsset(string $relativePath): ?string
    {
        $absolutePath = public_path($relativePath);
        if (! is_readable($absolutePath)) {
            return null;
        }

        try {
            $mimeType = function_exists('mime_content_type')
                ? (mime_content_type($absolutePath) ?: 'image/png')
                : 'image/png';
            $contents = file_get_contents($absolutePath);
            if ($contents === false) {
                return null;
            }

            return 'data:' . $mimeType . ';base64,' . base64_encode($contents);
        } catch (\Throwable $exception) {
            report($exception);

            return null;
        }
    }

    /**
     * Créer des notifications pour tous les utilisateurs lors de la confirmation
     */
    protected function createNotificationsForUsers(Guest $guest): void
    {
        $users = User::all();
        $message = "{$guest->display_name} a confirmé sa présence.";

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'guest_id' => $guest->id,
                'type' => 'rsvp_confirmed',
                'message' => $message,
            ]);
        }
    }

    /**
     * Envoyer des emails de notification à tous les utilisateurs
     */
    protected function sendEmailNotifications(Guest $guest): void
    {
        $users = User::all();

        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(new GuestConfirmationNotification($guest));
            } catch (\Exception $e) {
                // Log l'erreur mais ne bloque pas le processus
                report($e);
            }
        }
    }
}
