<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Beverage;
use App\Models\Guest;
use App\Models\GuestPreference;
use App\Models\Notification;
use App\Models\User;
use App\Mail\GuestConfirmationNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

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

        $filename = $data['event']['pdf_filename'] ?? 'invitation.pdf';

        return $pdf->download($filename);
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
        $message = "{$guest->display_name} a confirmé sa présence" .
                   ($guest->table ? " (Table: {$guest->table->name})" : "") . ".";

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
