<?php

namespace App\Services\WhatsApp;

use App\Models\Guest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use UltraMsg\WhatsAppApi;

class UltraMsgService
{
    protected ?WhatsAppApi $client = null;
    protected ?string $instance;
    protected ?string $token;

    public function __construct()
    {
        $this->instance = config('services.ultramsg.instance');
        $this->token = config('services.ultramsg.token');
    }

    /**
     * Envoie le lien d'invitation WhatsApp à un invité.
     *
     * @return array{sent: bool, response: mixed}
     */
    public function sendInvitation(Guest $guest): array
    {
        $phone = $this->sanitizePhone($guest->phone);

        if (! $phone) {
            throw new \InvalidArgumentException("Le numéro de téléphone de l'invité est invalide.");
        }

        if (! $guest->invitation_token) {
            $guest->forceFill([
                'invitation_token' => Str::uuid()->toString(),
            ])->save();
        }

        $invitationUrl = route('invitations.show', $guest->invitation_token);

        $message = $this->buildMessage($guest->display_name, $invitationUrl);

        $response = $this->getClient()->sendChatMessage($phone, $message);

        $sent = ! empty($response['sent']) || ! empty($response['id']);

        if ($sent) {
            $guest->forceFill([
                'whatsapp_sent_at' => now(),
            ])->save();
        }

        Log::info('Invitation WhatsApp envoyée', [
            'guest_id' => $guest->id,
            'phone' => $phone,
            'sent' => $sent,
            'response' => $response,
        ]);

        return [
            'sent' => $sent,
            'response' => $response,
        ];
    }

    protected function buildMessage(string $guestName, string $invitationUrl): string
    {
        return implode("\n", [
            "🎉 Bonjour {$guestName} !",
            "",
            "Nous avons le plaisir de vous inviter au mariage de Raphael & Daniella.",
            "",
            "👇 Cliquez sur le lien ci-dessous pour confirmer votre présence et retrouver tous les détails :",
            "🔗 {$invitationUrl}",
            "",
            "Dress code : All black 🖤",
        ]);
    }

    protected function sanitizePhone(string $phone): ?string
    {
        $prefix = config('services.ultramsg.default_recipient_prefix');

        $digits = preg_replace('/\D+/', '', $phone);

        if (! $digits) {
            return null;
        }

        if ($prefix) {
            $prefixDigits = ltrim($prefix, '+');
            if (! Str::startsWith($digits, $prefixDigits)) {
                $digits = ltrim($digits, '0');

                if ($digits === '') {
                    return null;
                }

                return $prefixDigits . $digits;
            }
        }

        return $digits;
    }

    /**
     * @throws \RuntimeException
     */
    protected function getClient(): WhatsAppApi
    {
        if (! $this->instance || ! $this->token) {
            throw new \RuntimeException("UltraMsg n'est pas configuré (instance ou token manquant).");
        }

        if (! $this->client) {
            $this->client = new WhatsAppApi($this->token, $this->instance);
        }

        return $this->client;
    }
}

