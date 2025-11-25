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

        $message = $this->buildMessage($guest->display_name, $guest->type, $invitationUrl);

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

    protected function buildMessage(string $guestName, ?string $guestType = '', string $invitationUrl): string
    {
        // Afficher le type uniquement si c'est un couple
        $greeting = $guestType === 'couple'
            ? "🎉 Bonjour {$guestType} {$guestName} !"
            : "🎉 Bonjour {$guestName} !";

        return implode("\n", [
            $greeting,
            "",
            "Nous avons le plaisir de vous inviter au mariage de Raphaël & Daniella.",
            "",
            "👇 Cliquez sur le lien ci-dessous pour confirmer votre présence et retrouver tous les détails :",
            "",
            "{$invitationUrl}",
            "",
            "ℹ️ Si vous ne pouvez pas cliquer sur le lien, pensez à enregistrer ce contact dans votre répertoire puis revenez sur ce message.",
            "",
            "Dress code : All black 🖤",
        ]);
    }

    /**
     * Formate et normalise un numéro de téléphone.
     * Méthode publique statique pour être utilisée dans les contrôleurs.
     *
     * @param string $phone Le numéro de téléphone à formater
     * @return string|null Le numéro formaté ou null si invalide
     */
    public static function formatPhone(string $phone): ?string
    {
        $prefix = config('services.ultramsg.default_recipient_prefix');

        $digits = preg_replace('/\D+/', '', $phone);

        if (! $digits) {
            return null;
        }

        // Vérifier si le numéro a déjà un préfixe international valide
        if (self::hasInternationalPrefixStatic($digits)) {
            return $digits;
        }

        // Si un préfixe par défaut est configuré et que le numéro ne l'a pas déjà
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

    protected function sanitizePhone(string $phone): ?string
    {
        return self::formatPhone($phone);
    }

    /**
     * Vérifie si un numéro de téléphone a déjà un préfixe international valide.
     * Les préfixes internationaux vont de 1 à 3 chiffres.
     * Méthode statique pour être utilisée dans formatPhone.
     *
     * @param string $digits Les chiffres du numéro de téléphone
     * @return bool True si le numéro a un préfixe international valide
     */
    protected static function hasInternationalPrefixStatic(string $digits): bool
    {
        if (strlen($digits) < 10) {
            return false;
        }

        // Liste des préfixes internationaux communs (1 à 3 chiffres)
        // Format: longueur => [préfixes possibles]
        $internationalPrefixes = [
            1 => ['1'], // USA, Canada
            2 => ['20', '27', '30', '31', '32', '33', '34', '36', '39', '40', '41', '43', '44', '45', '46', '47', '48', '49', '51', '52', '53', '54', '55', '56', '57', '58', '60', '61', '62', '63', '64', '65', '66', '81', '82', '84', '86', '90', '91', '92', '93', '94', '95', '98'],
            3 => ['212', '213', '216', '218', '220', '221', '222', '223', '224', '225', '226', '227', '228', '229', '230', '231', '232', '233', '234', '235', '236', '237', '238', '239', '240', '241', '242', '243', '244', '245', '246', '247', '248', '249', '250', '251', '252', '253', '254', '255', '256', '257', '258', '260', '261', '262', '263', '264', '265', '266', '267', '268', '269', '290', '291', '297', '298', '299', '350', '351', '352', '353', '354', '355', '356', '357', '358', '359', '370', '371', '372', '373', '374', '375', '376', '377', '378', '380', '381', '382', '383', '385', '386', '387', '389', '420', '421', '423', '500', '501', '502', '503', '504', '505', '506', '507', '508', '509', '590', '591', '592', '593', '594', '595', '596', '597', '598', '599', '670', '672', '673', '674', '675', '676', '677', '678', '679', '680', '681', '682', '683', '684', '685', '686', '687', '688', '689', '690', '691', '692', '850', '852', '853', '855', '856', '880', '886', '960', '961', '962', '963', '964', '965', '966', '967', '968', '970', '971', '972', '973', '974', '975', '976', '977', '992', '993', '994', '995', '996', '998'],
        ];

        // Vérifier les préfixes de 1 chiffre
        if (isset($internationalPrefixes[1]) && in_array(substr($digits, 0, 1), $internationalPrefixes[1])) {
            // Les numéros avec préfixe 1 (USA/Canada) font généralement 11 chiffres
            if (strlen($digits) >= 11) {
                return true;
            }
        }

        // Vérifier les préfixes de 2 chiffres
        if (strlen($digits) >= 11) {
            $twoDigitPrefix = substr($digits, 0, 2);
            if (isset($internationalPrefixes[2]) && in_array($twoDigitPrefix, $internationalPrefixes[2])) {
                return true;
            }
        }

        // Vérifier les préfixes de 3 chiffres
        if (strlen($digits) >= 12) {
            $threeDigitPrefix = substr($digits, 0, 3);
            if (isset($internationalPrefixes[3]) && in_array($threeDigitPrefix, $internationalPrefixes[3])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si un numéro de téléphone a déjà un préfixe international valide.
     * Les préfixes internationaux vont de 1 à 3 chiffres.
     *
     * @param string $digits Les chiffres du numéro de téléphone
     * @return bool True si le numéro a un préfixe international valide
     */
    protected function hasInternationalPrefix(string $digits): bool
    {
        return self::hasInternationalPrefixStatic($digits);
    }

    /**
     * Envoie le PDF de l'invitation via WhatsApp à un invité.
     *
     * @return array{sent: bool, response: mixed}
     */
    public function sendInvitationPdf(Guest $guest, string $pdfPath): array
    {
        $phone = $this->sanitizePhone($guest->phone);

        if (! $phone) {
            throw new \InvalidArgumentException("Le numéro de téléphone de l'invité est invalide.");
        }

        if (! file_exists($pdfPath)) {
            throw new \InvalidArgumentException("Le fichier PDF n'existe pas.");
        }

        // Afficher le type uniquement si c'est un couple
        $greeting = $guest->type === 'couple'
            ? "🎉 Bonjour couple {$guest->display_name} !"
            : "🎉 Bonjour {$guest->display_name} !";

        $message = implode("\n", [
            $greeting,
            "",
            "Nous avons le plaisir de vous inviter au mariage de Raphaël & Daniella.",
            "",
            "📎 Vous trouverez votre invitation en pièce jointe.",
            "",
            "Dress code : All black 🖤",
        ]);

        // Lire le contenu du PDF
        $pdfContent = file_get_contents($pdfPath);

        // Extraire le nom du fichier
        $filename = 'invitation_'.$guest->display_name.'.pdf';
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename); // Nettoyer le nom de fichier

        // Pour UltraMsg, le document peut être envoyé en base64 ou via une URL accessible publiquement
        // On utilise le format base64 avec le préfixe data:application/pdf;base64,
        // ou simplement le contenu base64 selon l'API
        $pdfBase64 = base64_encode($pdfContent);

        // Envoyer le document PDF via WhatsApp
        // Paramètres: to, filename, document (base64 ou URL), caption
        $response = $this->getClient()->sendDocumentMessage($phone, $filename, $pdfBase64, $message);

        $sent = ! empty($response['sent']) || ! empty($response['id']);

        if ($sent) {
            $guest->forceFill([
                'whatsapp_sent_at' => now(),
            ])->save();
        }

        Log::info('Invitation PDF WhatsApp envoyée', [
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

    /**
     * Envoie l'image de l'invitation via WhatsApp à un invité.
     *
     * @return array{sent: bool, response: mixed}
     */
    public function sendInvitationImage(Guest $guest, string $imagePath): array
    {
        $phone = $this->sanitizePhone($guest->phone);

        if (! $phone) {
            throw new \InvalidArgumentException("Le numéro de téléphone de l'invité est invalide.");
        }

        if (! file_exists($imagePath)) {
            throw new \InvalidArgumentException("Le fichier image n'existe pas.");
        }

        // Afficher le type uniquement si c'est un couple
        $greeting = $guest->type === 'couple'
            ? "🎉 Bonjour couple {$guest->display_name} !"
            : "🎉 Bonjour {$guest->display_name} !";

        $message = implode("\n", [
            $greeting,
            "",
            "Nous avons le plaisir de vous inviter au mariage de Raphaël & Daniella.",
            "",
            "📷 Vous trouverez votre invitation en image ci-dessous.",
            "",
            "Dress code : All black 🖤",
        ]);

        // Lire le contenu de l'image
        $imageContent = file_get_contents($imagePath);

        // Extraire le nom du fichier
        $filename = 'invitation_'.$guest->display_name.'.png';
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename); // Nettoyer le nom de fichier

        // Encoder l'image en base64
        $imageBase64 = base64_encode($imageContent);

        // Envoyer l'image via WhatsApp
        // Paramètres: to, image (base64), caption, priority, referenceId, nocache
        $response = $this->getClient()->sendImageMessage($phone, $imageBase64, $message);

        $sent = ! empty($response['sent']) || ! empty($response['id']);

        if ($sent) {
            $guest->forceFill([
                'whatsapp_sent_at' => now(),
            ])->save();
        }

        Log::info('Invitation Image WhatsApp envoyée', [
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
