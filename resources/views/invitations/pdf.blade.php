<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>{{ $event['page_title'] ?? 'Invitation de mariage' }}</title>
    <style>
        @font-face {
            font-family: 'Great Vibes';
            src: url('https://github.com/google/fonts/raw/main/ofl/greatvibes/GreatVibes-Regular.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @page {
            margin: 18mm 16mm;
        }

        body {
            margin: 0;
            font-family: "DejaVu Serif", serif;
            color: #2c2521;
            background: #f8f4ef;
        }

        .page {
            position: relative;
            min-height: 260mm;
            padding: 22mm 18mm;
            background: #fffdf9;
            border: 1px solid #f1d7b8;
            border-radius: 18px;
            overflow: hidden;
        }

        .bg-soft {
            position: absolute;
            inset: 0;
            opacity: 0.25;
        }

        .bg-soft img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .bouquet-overlay {
            position: absolute;
            bottom: 12mm;
            right: 14mm;
            width: 85mm;
            opacity: 0.22;
        }

        .bouquet-overlay img {
            width: 100%;
        }

        .content {
            position: relative;
            z-index: 10;
        }

        .headline {
            text-align: center;
            margin-bottom: 18mm;
        }

        .badge {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 999px;
            border: 1px solid #d9a870;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 11px;
            color: #bb8444;
            background: rgba(255, 240, 220, 0.55);
            font-family: "DejaVu Sans", sans-serif;
            font-weight: 600;
        }

        .guest-name {
            font-family: 'Great Vibes', 'DejaVu Serif', cursive;
            font-size: 22px;
            color: #b57533;
        }

        .sparkle {
            color: #b57533;
        }

        .couple-name {
            margin: 10px 0 4px;
            font-size: 34px;
            letter-spacing: 1px;
            color: #b57533;
            font-family: 'Great Vibes', 'DejaVu Serif', cursive;
            font-style: normal;
            font-weight: normal;
        }

        .date {
            font-size: 12px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #8c7c6b;
            margin: 0;
        }

        .invitation-card {
            border: 1px solid #efdcc6;
            border-radius: 16px;
            padding: 18px 22px;
            margin-bottom: 18mm;
            background: linear-gradient(145deg, rgba(255, 251, 245, 0.88), rgba(255, 234, 211, 0.9));
            font-family: "DejaVu Serif", serif;
        }

        .salutation {
            font-size: 13px;
            margin-bottom: 10px;
            line-height: 1.7;
        }

        .details-grid {
            display: table;
            width: 100%;
            margin-top: 8px;
        }

        .details-row {
            display: table-row;
        }

        .details-label,
        .details-value {
            display: table-cell;
            padding: 6px 0;
            vertical-align: top;
            font-size: 12px;
            font-family: "DejaVu Sans", sans-serif;
        }

        .details-label {
            width: 50%;
            color: #bb8444;
            font-weight: bold;
        }

        .program-section {
            margin-bottom: 16mm;
        }

        .section-title {
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 13px;
            color: #bb8444;
            margin-bottom: 8px;
            font-family: "DejaVu Sans", sans-serif;
            font-weight: 600;
        }

        .dress-code {
            font-family: 'Great Vibes', 'DejaVu Serif', cursive;
            font-size: 18px;
            color: #b57533;
        }

        .timeline {
            border: 1px solid #efdcc6;
            border-radius: 14px;
            overflow: hidden;
        }

        .timeline-row {
            display: table;
            width: 100%;
        }

        .timeline-cell {
            display: table-cell;
            padding: 12px 14px;
            font-size: 12px;
            vertical-align: top;
        }

        .timeline-cell.time {
            width: 70px;
            font-weight: bold;
            color: #b57533;
        }

        .note {
            margin-top: 10px;
            font-style: italic;
            color: #7a6b5c;
            font-size: 11px;
        }

        .qr-section {
            text-align: center;
            margin-top: 20mm;
        }

        .qr-section img {
            width: 135px;
            height: 135px;
            border: 6px solid #fff;
            box-shadow: 0 10px 30px rgba(69, 52, 32, 0.16);
            border-radius: 18px;
        }

        .qr-caption {
            margin-top: 10px;
            font-size: 11px;
            color: #605349;
        }

        .footer {
            margin-top: 12mm;
            text-align: center;
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #8c7c6b;
        }
    </style>
</head>

<body>
    <div class="page">
        @if (!empty($backgroundImage))
            <div class="bg-soft">
                <img src="{{ $backgroundImage }}" alt="Fond décoratif">
            </div>
        @endif
        @if (!empty($bouquetImage))
            <div class="bouquet-overlay">
                <img src="{{ $bouquetImage }}" alt="Décoration florale">
            </div>
        @endif

        <div class="content">
            <div class="headline">
                <span class="badge">Invitation officielle</span>
                <h1 class="couple-name">{{ $event['couple_names'] ?? '' }}</h1>
                <p class="date">{{ $event['date_long'] ?? '' }}</p>
            </div>

            <div class="invitation-card">
                <p class="salutation">
                    Cher·e <span class="guest-name">{{ $guest->display_name }}</span>,<br>
                    C’est avec une immense joie que nous vous invitons à témoigner de nos vœux sacrés
                    <span class="sparkle">💍</span> et à partager une soirée scintillante
                    <span class="sparkle">✨</span>. Votre présence chérira notre histoire et illuminera cette journée.
                </p>
                <div class="details-grid">
                    @if ($guest->table)
                        <div class="details-row">
                            <div class="details-label">Table</div>
                            <div class="details-value">{{ $guest->table->name }}</div>
                        </div>
                    @endif
                    <div class="details-row">
                        <div class="details-label">Dress code</div>
                        <div class="details-value">
                            <span class="dress-code">{{ $event['dress_code'] ?? 'Chic et Élégant' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="program-section">
                <div class="section-title">Programme de la journée</div>
                <div class="timeline">
                    <div class="timeline-row" style="background: rgba(255, 248, 239, 0.72);">
                        <div class="timeline-cell time">{{ $event['ceremony_time'] ?? '' }}</div>
                        <div class="timeline-cell">
                            <strong>{{ $event['ceremony_title'] ?? '' }}</strong><br>
                            {!! nl2br(e($event['ceremony_location'] ?? '')) !!}
                        </div>
                    </div>
                    <div class="timeline-row">
                        <div class="timeline-cell time">{{ $event['reception_time'] ?? '' }}</div>
                        <div class="timeline-cell">
                            <strong>{{ $event['reception_title'] ?? '' }}</strong><br>
                            {!! nl2br(e($event['reception_location'] ?? '')) !!}
                        </div>
                    </div>
                </div>
                <p class="note">
                    Merci d’arriver avec quelques minutes d’avance afin de partager pleinement chaque instant.
                    Le respect de l’heure est vivement souhaité.
                </p>
            </div>

            <div class="qr-section">
                @if (!empty($qrCodeDataUri))
                    <img src="{{ $qrCodeDataUri }}" alt="QR code de confirmation">
                @endif
                <p class="qr-caption">
                    Confirmez votre présence via le QR code<br>
                    ou sur&nbsp;: {{ $invitationUrl }}
                </p>
            </div>

            <div class="footer">
                Avec toute notre affection — Raphael &amp; Daniella
            </div>
        </div>
    </div>
</body>

</html>

