<x-mail::message>
# Réinitialisation de votre mot de passe

Bonjour,

Vous avez demandé à réinitialiser votre mot de passe. Utilisez le code suivant pour continuer :

## Code de vérification

<div style="text-align: center; margin: 30px 0;">
    <div style="display: inline-block; padding: 20px 40px; background-color: #f3f4f6; border-radius: 8px; font-size: 32px; font-weight: bold; letter-spacing: 8px; font-family: monospace;">
        {{ $code }}
    </div>
</div>

**Ce code est valide pendant 15 minutes.**

Si vous n'avez pas demandé cette réinitialisation, ignorez simplement cet email.

Merci,<br>
{{ config('app.name') }}
</x-mail::message>
