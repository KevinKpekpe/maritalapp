<?php

namespace App\Http\Controllers;

use App\Models\Beverage;
use App\Models\Guest;
use App\Models\GuestPreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function show(string $token): View
    {
        $guest = Guest::with(['table', 'preferences.beverage'])
            ->where('invitation_token', $token)
            ->firstOrFail();

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
            'dress_code' => 'Chic et Élégant',
        ];
        $event['pdf_filename'] = 'Invitation-' . Str::slug($event['couple_names'] ?? 'mariage', '-') . '.pdf';

        $pdfAssets = [
            'background' => $this->encodePublicAsset('invitations/fond.jpeg'),
            'bouquet' => $this->encodePublicAsset('invitations/bouquet.png'),
        ];

        $allBeverages = Beverage::orderBy('name')->get();
        $beverages = $allBeverages->groupBy('category')->map(fn ($group) => $group->values());
        $beverageMap = $allBeverages->keyBy('id')->map->name;

        return view('invitations.invitation', [
            'guest' => $guest,
            'event' => $event,
            'invitationUrl' => $invitationUrl,
            'qrCodeUrl' => $qrCodeUrl,
            'qrCodeDataUri' => $qrCodeDataUri,
            'beverages' => $beverages,
            'beverageMap' => $beverageMap,
            'downloadNotice' => $downloadNotice,
            'pdfAssets' => $pdfAssets,
        ]);
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
            $mimeType = mime_content_type($absolutePath) ?: 'image/png';
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
}
