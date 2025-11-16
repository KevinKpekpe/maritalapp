<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle confirmation de présence</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #b5824d;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .guest-info {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info-row {
            margin: 10px 0;
        }
        .label {
            font-weight: bold;
            color: #b5824d;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Nouvelle confirmation de présence</h1>
    </div>
    <div class="content">
        <p>Bonjour,</p>
        <p>Nous vous informons qu'un invité a confirmé sa présence pour le mariage.</p>

        <div class="guest-info">
            <h2>Informations de l'invité</h2>
            <div class="info-row">
                <span class="label">Nom :</span> {{ $guest->display_name }}
            </div>
            @if($guest->email)
            <div class="info-row">
                <span class="label">Email :</span> {{ $guest->email }}
            </div>
            @endif
            @if($guest->phone)
            <div class="info-row">
                <span class="label">Téléphone :</span> {{ $guest->phone }}
            </div>
            @endif
            @if($guest->table)
            <div class="info-row">
                <span class="label">Table :</span> {{ $guest->table->name }}
            </div>
            @endif
            <div class="info-row">
                <span class="label">Type :</span> {{ $guest->type === 'couple' ? 'Couple' : 'Solo' }}
            </div>
            <div class="info-row">
                <span class="label">Date de confirmation :</span> {{ $guest->rsvp_confirmed_at->format('d/m/Y à H:i') }}
            </div>
        </div>

        <p>Vous pouvez consulter les détails dans votre application de gestion.</p>

        <div class="footer">
            <p>Cet email a été envoyé automatiquement depuis l'Application de Gestion de Mariage</p>
        </div>
    </div>
</body>
</html>

