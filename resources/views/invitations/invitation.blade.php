<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event['page_title'] ?? 'Invitation de mariage' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Playfair+Display:wght@400;600;700&family=Outfit:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        accent: "#D97B2F",
                        "accent-soft": "#F6D7AC",
                        "accent-deep": "#A85E24",
                        ink: "#2C2521",
                        "ink-soft": "rgba(44,37,33,0.72)",
                        dusk: "#1E1B25",
                        linen: "#F8F4EF",
                        pearl: "#FDFBFA"
                    },
                    fontFamily: {
                        display: ['"Great Vibes"', 'cursive'],
                        serif: ['"Playfair Display"', 'serif'],
                        sans: ['"Outfit"', '"Myriad Variable Concept"', '"Myriad Pro"', '"Segoe UI"', 'sans-serif']
                    },
                    boxShadow: {
                        soft: "0 22px 65px rgba(0,0,0,0.12)",
                        glow: "0 18px 45px rgba(217,123,47,0.35)"
                    },
                    keyframes: {
                        'fade-up': {
                            '0%': { opacity: '0', transform: 'translateY(18px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        shimmer: {
                            '0%': { backgroundPosition: '-200% 0' },
                            '100%': { backgroundPosition: '200% 0' }
                        }
                    },
                    animation: {
                        'fade-up': 'fade-up 0.9s ease-out both',
                        shimmer: 'shimmer 3s ease-in-out infinite'
                    }
                }
            }
        };
    </script>
    <style>
        body {
            font-family: "Outfit", "Myriad Variable Concept", "Myriad Pro", "Segoe UI", sans-serif;
            background: radial-gradient(circle at top left, rgba(217, 123, 47, 0.08), transparent 42%),
                radial-gradient(circle at bottom right, rgba(104, 82, 189, 0.08), transparent 46%),
                #f9f5f1;
            color: #2C2521;
        }

        .bg-linear-to-r {
            background-image: linear-gradient(to right, var(--tw-gradient-stops));
        }

        body.no-scroll {
            overflow: hidden;
        }

        .invitation-frame {
            opacity: 0;
            transform: translateY(32px) scale(0.97);
            filter: blur(12px);
            transition: opacity 1.4s ease, transform 1.6s cubic-bezier(0.16, 1, 0.3, 1), filter 1.2s ease;
            pointer-events: none;
        }

        .invitation-frame.pre-reveal {
            opacity: 0.22;
            transform: translateY(18px) scale(0.982);
            filter: blur(8px);
        }

        .invitation-frame.revealed {
            opacity: 1;
            transform: translateY(0) scale(1);
            filter: blur(0);
            pointer-events: auto;
        }

        .grain-overlay::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200' viewBox='0 0 200 200'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='1.2' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.08'/%3E%3C/svg%3E");
            mix-blend-mode: overlay;
        }

        .ornate-card {
            position: relative;
            border-radius: 36px;
            overflow: hidden;
            background: rgba(253, 251, 250, 0.88);
            backdrop-filter: blur(22px);
        }

        .ornate-card::before {
            content: "";
            position: absolute;
            inset: 1px;
            border-radius: 34px;
            background: linear-gradient(135deg, rgba(217, 123, 47, 0.08), rgba(255, 255, 255, 0.9));
            border: 1px solid rgba(217, 123, 47, 0.18);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.45);
        }

        .ornate-card>* {
            position: relative;
            z-index: 1;
        }

        .section-title {
            letter-spacing: 0.24em;
        }

        .divider {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
        }

        .divider::before,
        .divider::after {
            content: "";
            width: 64px;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(217, 123, 47, 0.8), transparent);
        }

        .timeline {
            position: relative;
        }

        .timeline::before {
            content: "";
            position: absolute;
            top: 1.75rem;
            bottom: 1.75rem;
            left: 1.5rem;
            width: 2px;
            background: linear-gradient(to bottom, rgba(217, 123, 47, 0.4), rgba(217, 123, 47, 0));
        }

        @media (max-width: 1024px) {
            .timeline::before {
                left: 1.25rem;
            }
        }

        @media (max-width: 768px) {
            .timeline::before {
                display: none;
            }
        }

        .timeline-point {
            position: relative;
        }

        .timeline-point::before {
            content: "";
            position: absolute;
            left: -2.05rem;
            top: 0.6rem;
            width: 12px;
            height: 12px;
            background: linear-gradient(145deg, #F6D7AC, #D97B2F);
            border-radius: 9999px;
            box-shadow: 0 0 0 6px rgba(217, 123, 47, 0.12);
        }

        .schedule-grid {
            display: grid;
            gap: 1.25rem;
        }

        @media (min-width: 640px) {
            .schedule-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        .schedule-card {
            position: relative;
            display: flex;
            gap: 1.25rem;
            align-items: flex-start;
            padding: 1.75rem 1.6rem;
            border-radius: 30px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 240, 224, 0.82));
            border: 1px solid rgba(217, 123, 47, 0.22);
            box-shadow: 0 16px 40px rgba(48, 41, 33, 0.12);
            transition: transform 0.35s ease, box-shadow 0.35s ease;
        }

        .schedule-card::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            border: 1px solid rgba(255, 255, 255, 0.45);
            pointer-events: none;
        }

        .schedule-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 28px 55px rgba(48, 41, 33, 0.18);
        }

        .schedule-icon {
            display: grid;
            place-items: center;
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 1.5rem;
            background: linear-gradient(135deg, rgba(217, 123, 47, 0.18), rgba(255, 203, 150, 0.38));
            color: #D97B2F;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.65);
        }

        .schedule-time {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.3rem 1.05rem;
            border-radius: 999px;
            background: rgba(217, 123, 47, 0.12);
            color: #D97B2F;
            font-weight: 700;
            letter-spacing: 0.32em;
            font-size: 0.7rem;
            text-transform: uppercase;
        }

        .schedule-content h3 {
            font-family: "Playfair Display", serif;
            font-size: 1.35rem;
            margin-top: 0.9rem;
            color: #2C2521;
        }

        .schedule-content p {
            margin-top: 0.6rem;
            font-size: 0.95rem;
            line-height: 1.65;
            color: rgba(44, 37, 33, 0.72);
        }

        .map-card {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            padding: 2.4rem 2.1rem;
            border-radius: 32px;
            background: linear-gradient(145deg, rgba(255, 253, 250, 0.95), rgba(255, 236, 220, 0.86));
            border: 1px solid rgba(217, 123, 47, 0.15);
            box-shadow: 0 18px 55px rgba(43, 34, 29, 0.12);
            overflow: hidden;
        }

        .map-card::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            border: 1px solid rgba(255, 255, 255, 0.6);
            pointer-events: none;
        }

        .map-card__badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            align-self: flex-start;
            padding: 0.45rem 1.3rem;
            border-radius: 999px;
            background: rgba(217, 123, 47, 0.12);
            color: #D97B2F;
            letter-spacing: 0.32em;
            text-transform: uppercase;
            font-size: 0.68rem;
            font-weight: 600;
        }

        .map-card__title {
            font-family: "Playfair Display", serif;
            font-size: 1.75rem;
            color: #2C2521;
            letter-spacing: 0.02em;
        }

        .map-card__address {
            font-size: 0.98rem;
            line-height: 1.7;
            color: rgba(44, 37, 33, 0.7);
            white-space: pre-line;
        }

        .map-container {
            position: relative;
            width: 100%;
            height: 280px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.7), 0 16px 38px rgba(32, 24, 20, 0.14);
            background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.85), rgba(255, 240, 224, 0.65));
        }

        .map-container iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .map-placeholder {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            color: rgba(44, 37, 33, 0.55);
            font-style: italic;
            font-size: 0.95rem;
        }

        .map-actions {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            align-self: flex-start;
            padding: 0.4rem 1.15rem;
            border-radius: 999px;
            background: rgba(33, 29, 43, 0.08);
            color: rgba(44, 37, 33, 0.82);
            font-size: 0.78rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            transition: background 0.25s ease, transform 0.25s ease;
        }

        .map-actions:hover {
            background: rgba(44, 37, 33, 0.12);
            transform: translateY(-2px);
        }

        .map-actions svg {
            width: 16px;
            height: 16px;
        }

        @media (max-width: 768px) {
            .map-card {
                padding: 1.9rem 1.6rem;
            }

            .map-container {
                height: 240px;
            }
        }

        .schedule-highlight {
            font-weight: 600;
            color: #2C2521;
        }

        .drink-option {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.9rem 1.6rem;
            border-radius: 999px;
            border: 1px solid rgba(217, 123, 47, 0.3);
            background: rgba(255, 255, 255, 0.78);
            color: #D97B2F;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease, background 0.25s ease, color 0.25s ease;
        }

        .drink-option:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 32px rgba(217, 123, 47, 0.2);
        }

        .drink-option.is-selected {
            background: linear-gradient(135deg, #D97B2F, #F3A552);
            border-color: rgba(255, 255, 255, 0.55);
            color: #fff;
            box-shadow: 0 22px 40px rgba(217, 123, 47, 0.3);
        }

        .drink-option.is-selected::after {
            content: "\2713";
            font-size: 0.85em;
            margin-left: 0.4rem;
        }

        .drink-option:focus-visible {
            outline: 3px solid rgba(217, 123, 47, 0.45);
            outline-offset: 3px;
        }

        .qr-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            padding: 1.6rem;
            border-radius: 30px;
            border: 1px solid rgba(217, 123, 47, 0.25);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.96), rgba(255, 225, 197, 0.28));
            box-shadow: 0 22px 45px rgba(217, 123, 47, 0.25);
        }

        .qr-image {
            width: 13.5rem;
            max-width: 100%;
            border-radius: 24px;
            border: 6px solid #fff;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.65), 0 18px 35px rgba(48, 41, 33, 0.18);
        }

        .qr-caption {
            font-size: 0.75rem;
            color: rgba(44, 37, 33, 0.7);
            text-align: center;
        }

        .download-button__overlay {
            position: absolute;
            inset: 0;
            border-radius: 999px;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.28), rgba(255, 255, 255, 0));
            opacity: 0;
            transition: opacity 0.35s ease;
        }

        .download-button__content {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            gap: 0.8rem;
        }

        .download-button:hover .download-button__overlay,
        .download-button:focus-visible .download-button__overlay {
            opacity: 1;
        }

        .download-spinner {
            display: none;
            width: 1.15rem;
            height: 1.15rem;
        }

        .download-spinner svg {
            width: 100%;
            height: 100%;
            animation: spin 1s linear infinite;
        }

        .download-button.is-loading .download-icon {
            display: none;
        }

        .download-button.is-loading .download-spinner {
            display: inline-flex;
        }

        @keyframes slide-down {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .animate-slide-down {
            animation: slide-down 0.4s ease-out;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .hero-background {
            background-image: linear-gradient(rgba(18, 14, 14, 0.68), rgba(18, 14, 14, 0.55)), url("{{ asset('invitations/DSC_1133.jpg') }}");
            background-size: cover;
            background-position: center 30%;
        }

        @media (min-width: 1024px) {
            .hero-background {
                background-position: center 8%;
            }
        }

        .bouquet-deco {
            position: absolute;
            width: clamp(140px, 18vw, 220px);
            opacity: 0.85;
            filter: drop-shadow(0 24px 45px rgba(0, 0, 0, 0.18));
            pointer-events: none;
        }

        .bouquet-deco--left {
            top: -60px;
            left: -70px;
            transform: rotate(-8deg);
        }

        @media (max-width: 768px) {
            .bouquet-deco {
                width: clamp(120px, 38vw, 180px);
                opacity: 0.75;
            }

            .bouquet-deco--left {
                top: -50px;
                left: -35px;
            }

            .bouquet-deco--right {
                bottom: -55px;
                right: -30px;
            }
        }

        .bouquet-deco--right {
            bottom: -70px;
            right: -60px;
            transform: rotate(12deg) scaleX(-1);
        }

        @media (max-width: 1024px) {
            .bouquet-deco {
                opacity: 0.6;
            }
        }

        .reveal-overlay {
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at 18% 16%, rgba(248, 244, 239, 0.96), rgba(217, 123, 47, 0.16)),
                linear-gradient(160deg, rgba(32, 32, 44, 0.32), rgba(255, 240, 228, 0.92));
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 2.5rem;
            z-index: 40;
            transition: opacity 0.9s ease-in-out, visibility 0.9s ease-in-out, background 1.4s ease-in-out;
            backdrop-filter: blur(18px);
            padding: clamp(2rem, 8vw, 4.5rem) clamp(1.4rem, 6vw, 3.5rem);
            overflow: hidden;
        }

        .reveal-overlay.hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .reveal-overlay.opening {
            background: radial-gradient(circle at 20% 18%, rgba(248, 244, 239, 0.76), rgba(217, 123, 47, 0.1)),
                linear-gradient(160deg, rgba(32, 32, 44, 0.08), rgba(255, 240, 228, 0.8));
            backdrop-filter: blur(8px);
        }

        .overlay-decor {
            position: absolute;
            pointer-events: none;
            opacity: 0.58;
            filter: drop-shadow(0 28px 70px rgba(32, 21, 12, 0.35));
            animation: float-soft 6s ease-in-out infinite;
        }

        .overlay-decor--left {
            width: clamp(150px, 26vw, 260px);
            left: clamp(-48px, -6vw, -20px);
            top: clamp(12%, 20vw, 18%);
            transform: rotate(-12deg);
            animation-delay: -1.2s;
        }

        .overlay-decor--right {
            width: clamp(138px, 24vw, 240px);
            right: clamp(-42px, -5vw, -18px);
            bottom: clamp(8%, 12vw, 16%);
            transform: scaleX(-1) rotate(10deg);
            animation-delay: -2.4s;
        }

        @keyframes float-soft {

            0%,
            100% {
                transform: translateY(0) scale(1);
            }

            50% {
                transform: translateY(-6px) scale(1.02);
            }
        }

        .envelope {
            position: relative;
            width: clamp(280px, 78vw, 440px);
            height: clamp(180px, 32vw, 300px);
            background: #fdf3e7;
            border-radius: 22px;
            box-shadow: 0 30px 80px rgba(32, 21, 12, 0.28);
            overflow: hidden;
            perspective: 1400px;
            cursor: pointer;
            transition: transform 0.8s ease;
        }

        .envelope:hover {
            transform: translateY(-6px);
        }

        .envelope__front,
        .envelope__flap {
            position: absolute;
            inset: 0;
            border-radius: 22px;
        }

        .envelope__front {
            background: linear-gradient(160deg, rgba(238, 209, 179, 0.98), rgba(252, 236, 217, 0.98));
            border: 1px solid rgba(168, 94, 36, 0.18);
        }

        .envelope__flap {
            background: linear-gradient(180deg, rgba(234, 202, 163, 0.9), rgba(217, 123, 47, 0.82));
            clip-path: polygon(0 0, 50% 56%, 100% 0);
            transform-origin: top;
            transform: rotateX(0deg);
            transition: transform 1s ease-in-out;
            border-bottom: 1px solid rgba(168, 94, 36, 0.28);
        }

        .envelope__side {
            position: absolute;
            bottom: 0;
            width: 50%;
            height: 60%;
            background: linear-gradient(150deg, rgba(222, 187, 146, 0.95), rgba(244, 221, 195, 0.95));
            clip-path: polygon(0 0, 100% 0, 50% 85%);
        }

        .envelope__side--left {
            left: 0;
            transform: rotateY(0deg);
        }

        .envelope__side--right {
            right: 0;
            transform: scaleX(-1);
        }

        .envelope__seal {
            position: absolute;
            top: 44%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: clamp(40px, 7vw, 58px);
            height: clamp(40px, 7vw, 58px);
            background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.55), rgba(217, 123, 47, 0.84));
            border-radius: 999px;
            box-shadow: 0 12px 30px rgba(217, 123, 47, 0.35);
            border: 1px solid rgba(143, 82, 29, 0.35);
            display: grid;
            place-items: center;
            color: rgba(255, 255, 255, 0.92);
            font-size: clamp(18px, 3vw, 26px);
        }

        .envelope__letter {
            position: absolute;
            inset: 16px;
            background: #fcfbf9;
            border-radius: 12px;
            border: 1px solid rgba(217, 123, 47, 0.24);
            box-shadow: 0 16px 40px rgba(100, 76, 53, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 0.55rem;
            padding: 1.75rem 1.75rem 1.55rem;
            transform: translateY(26%);
            transition: transform 1.2s ease-in-out 0.25s, opacity 0.9s ease-in-out 0.35s;
            opacity: 0;
        }

        .envelope__letter::before,
        .envelope__letter::after {
            content: "";
            position: absolute;
            width: clamp(68px, 12vw, 110px);
            aspect-ratio: 1 / 1;
            background: url("{{ asset('invitations/bouquet.png') }}") no-repeat center/contain;
            opacity: 0.58;
            pointer-events: none;
        }

        .envelope__letter::before {
            top: -14px;
            left: -12px;
        }

        .envelope__letter::after {
            bottom: -18px;
            right: -20px;
            transform: scaleX(-1) rotate(-4deg);
        }

        .envelope__letter span {
            font-family: "Playfair Display", serif;
            font-weight: 600;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            font-size: clamp(0.7rem, 1.6vw, 0.9rem);
            color: #A85E24;
        }

        .envelope__letter em {
            font-family: "Great Vibes", cursive;
            font-size: clamp(1.2rem, 3.4vw, 1.8rem);
            color: #D97B2F;
        }

        .envelope__initials {
            font-family: "Great Vibes", cursive;
            font-size: clamp(1.6rem, 4vw, 2.2rem);
            color: rgba(168, 94, 36, 0.92);
            letter-spacing: 0.18em;
        }

        .envelope__letter small {
            font-family: "Outfit", "Segoe UI", sans-serif;
            font-size: clamp(0.55rem, 1.4vw, 0.75rem);
            letter-spacing: 0.32em;
            text-transform: uppercase;
            color: rgba(44, 37, 33, 0.58);
        }

        .envelope__shine {
            position: absolute;
            inset: -20%;
            background: radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.6), transparent 55%);
            mix-blend-mode: screen;
            opacity: 0.9;
            pointer-events: none;
        }

        .envelope.open .envelope__flap {
            transform: rotateX(180deg);
        }

        .envelope.open .envelope__letter {
            transform: translateY(-8%);
            opacity: 1;
        }

        .envelope.open:hover {
            transform: none;
        }

        .reveal-instruction {
            font-size: clamp(0.9rem, 2.1vw, 1.2rem);
            text-transform: uppercase;
            letter-spacing: 0.28em;
            color: rgba(32, 32, 44, 0.65);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .reveal-instruction::after {
            content: "\2728";
            font-size: 1.1em;
        }

        .reveal-overlay.opening .reveal-instruction {
            opacity: 0;
            transform: translateY(12px);
        }

        .snow-layer {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 12;
            overflow: hidden;
            opacity: 0.9;
        }

        .snow-flake {
            position: absolute;
            top: -10vh;
            width: clamp(4px, 0.9vw, 7px);
            height: clamp(4px, 0.9vw, 7px);
            border-radius: 999px;
            background: radial-gradient(circle at 35% 35%, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.32) 55%, rgba(255, 255, 255, 0));
            box-shadow: 0 0 8px rgba(255, 255, 255, 0.45);
            animation: snow-fall linear infinite;
        }

        @keyframes snow-fall {
            0% {
                transform: translate3d(var(--x-offset, 0), 0, 0) scale(1);
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 1;
            }

            100% {
                transform: translate3d(var(--x-offset, 0), 110vh, 0) scale(0.85);
                opacity: 0;
            }
        }
    </style>
</head>

@php
    $selectedBeverageIds = $guest->preferences->pluck('beverage_id')->filter()->values();
    $selectedBeverageNames = $selectedBeverageIds
        ->map(fn ($id) => $beverageMap[$id] ?? null)
        ->filter()
        ->values();
    $coupleInitials = collect(preg_split('/\s+/', $event['couple_names'] ?? '', -1, PREG_SPLIT_NO_EMPTY))
        ->reject(fn ($part) => in_array(mb_strtolower($part), ['et', 'and', '&', 'y', 'e']))
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->take(2)
        ->implode(' & ');
@endphp
<body class="overflow-x-hidden no-scroll">
    @if (session('status') || session('preferences_status'))
        <div id="success-alert" class="fixed top-0 left-0 right-0 z-50 flex justify-center items-center p-4 animate-slide-down">
            <div class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl max-w-md w-full flex items-center gap-3 border-2 border-green-400">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <div class="flex-1">
                    <p class="font-semibold">{{ session('status') ?? session('preferences_status') }}</p>
                </div>
                <button onclick="document.getElementById('success-alert').remove()" class="text-white hover:text-green-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif
    <div class="snow-layer" aria-hidden="true"></div>
    <div id="reveal-overlay" class="reveal-overlay" tabindex="0">
        <img src="{{ asset('invitations/bouquet.png') }}" alt="Décoration bouquet" class="overlay-decor overlay-decor--left">
        <img src="{{ asset('invitations/bouquet.png') }}" alt="Décoration bouquet" class="overlay-decor overlay-decor--right">
        <audio id="invitation-audio" preload="auto">
            <source src="{{ asset('invitations/melo.webm') }}" type="audio/webm">
        </audio>
        <div id="envelope" class="envelope" role="button" aria-label="Ouvrir l'invitation" tabindex="0">
            <div class="envelope__shine"></div>
            <div class="envelope__flap"></div>
            <div class="envelope__front"></div>
            <div class="envelope__side envelope__side--left"></div>
            <div class="envelope__side envelope__side--right"></div>
            <div class="envelope__seal">&#10084;</div>
            <div class="envelope__letter">
                <span>Invitation</span>
                <p class="envelope__initials">{{ $coupleInitials }}</p>
                <em>{{ $event['couple_names'] ?? '' }}</em>
                <small>{{ $event['date_long'] ?? '' }}</small>
            </div>
        </div>
        <p class="reveal-instruction">Cliquez pour ouvrir</p>
    </div>
    <main
        class="invitation-frame relative w-full max-w-7xl mx-auto shadow-soft bg-pearl rounded-[36px] overflow-hidden transition-all duration-700 ease-out">
        <div class="absolute inset-0 opacity-40 blur-[120px] pointer-events-none"
            style="background: radial-gradient(circle at 20% 20%, rgba(217,123,47,0.35), transparent 55%), radial-gradient(circle at 80% 85%, rgba(73, 50, 139, 0.3), transparent 60%);">
        </div>
        <section
            class="relative min-h-screen flex items-center justify-center text-white overflow-hidden rounded-[36px]">
            <div class="absolute inset-0 hero-background grain-overlay"></div>
            <div
                class="relative flex flex-col items-center gap-6 px-6 sm:px-10 lg:px-16 py-24 sm:py-28 text-center max-w-3xl animate-fade-up">
                <span
                    class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-white/10 border border-white/30 font-medium tracking-[0.35em] uppercase text-xs sm:text-sm backdrop-blur">Save
                    the Date</span>
                <h1
                    class="text-4xl sm:text-5xl lg:text-6xl font-display tracking-wide leading-tight text-white drop-shadow-soft">
                    {{ $event['couple_names'] ?? 'Nos Mariés' }}</h1>
                <p class="text-white/80 text-sm sm:text-base tracking-[0.26em] uppercase">Samedi 29 novembre 2025</p>

                <div class="relative flex items-center justify-center">
                    <div class="absolute -inset-4 rounded-full bg-linear-to-r from-white/10 to-white/0 blur-2xl">
                    </div>
                    <img src="{{ asset('invitations/savethedate.png') }}" alt="Save the date"
                        class="relative w-48 sm:w-64 lg:w-88 opacity-95 drop-shadow-2xl">
                </div>
            </div>
        </section>

        <section class="relative px-6 sm:px-10 lg:px-16 py-20 sm:py-24 bg-no-repeat bg-cover"
            style="background-image: linear-gradient(rgba(255,247,244,0.95), rgba(255,233,224,0.88)), url('{{ asset('invitations/invitationuse.jpeg') }}');">
            <div
                class="relative ornate-card max-w-5xl mx-auto px-7 sm:px-12 lg:px-16 py-14 sm:py-16 shadow-2xl border border-white/20">
                <img src="{{ asset('invitations/bouquet.png') }}" alt="Décoration bouquet"
                    class="bouquet-deco bouquet-deco--left">
                <img src="{{ asset('invitations/bouquet.png') }}" alt="Décoration bouquet"
                    class="bouquet-deco bouquet-deco--right">
                <div class="flex flex-col lg:flex-row items-center lg:items-start gap-12 lg:gap-14">
                    <div class="flex-1 space-y-6 lg:space-y-8 text-center lg:text-left animate-fade-up">
                        <div class="space-y-4">
                            <span
                                class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-full bg-accent/10 text-accent font-semibold tracking-[0.28em] uppercase text-xs sm:text-sm shadow-soft">La
                                célébration</span>
                            <div class="space-y-2">
                                <h2 class="font-display text-4xl sm:text-5xl text-accent leading-tight">{{ $event['couple_names'] ?? '' }}</h2>
                                <p
                                    class="font-serif text-sm sm:text-base tracking-[0.26em] uppercase text-ink font-medium">
                                    {{ $event['date_long'] ?? '' }}</p>
                                <p class="font-display text-3xl text-accent/90">Invitation pour {{ $guest->display_name }}</p>
                                @if ($guest->table)
                                    <p class="font-display text-2xl text-accent/80">Table : {{ $guest->table->name }}</p>
                                @endif
                            </div>
                        </div>
                        <p
                            class="text-base sm:text-lg lg:text-xl text-ink-soft leading-relaxed max-w-xl mx-auto lg:mx-0">
                            C’est avec une immense joie que nous vous invitons à témoigner de nos vœux sacrés 💍 et à
                            partager une soirée scintillante ✨. Votre présence chérira notre histoire et illuminera
                            cette journée.
                        </p>
                        <p class="font-display text-2xl text-accent/80">Dress code : {{ $event['dress_code'] ?? 'Chic et Élégant' }}</p>
                        <div class="schedule-grid">
                            <article class="schedule-card">
                                <div class="schedule-icon">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="8.5"></circle>
                                        <path d="M12 7v5l3 1.8"></path>
                                    </svg>
                                </div>
                                <div class="schedule-content">
                                    <span class="schedule-time">{{ $event['ceremony_time'] ?? '10h00' }}</span>
                                    <h3>{{ $event['ceremony_title'] ?? 'Cérémonie' }}</h3>
                                    <p>{!! nl2br(e($event['ceremony_location'] ?? '')) !!}</p>
                                </div>
                            </article>
                            <article class="schedule-card">
                                <div class="schedule-icon">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M12 2c3.314 0 6 2.582 6 5.77 0 2.371-1.5 4.431-3.438 5.327L14 18l-2-1-2 1-.563-4.903C7.5 12.201 6 10.141 6 7.77 6 4.582 8.686 2 12 2Z">
                                        </path>
                                        <path d="M8.5 20h7"></path>
                                    </svg>
                                </div>
                                <div class="schedule-content">
                                    <span class="schedule-time">{{ $event['reception_time'] ?? '19h30' }}</span>
                                    <h3>{{ $event['reception_title'] ?? 'Réception' }}</h3>
                                    <p>{!! nl2br(e($event['reception_location'] ?? '')) !!}</p>
                                </div>
                            </article>
                        </div>

                        <p class="pt-2 font-semibold text-ink uppercase tracking-[0.24em] text-xs sm:text-sm">
                            Cordialement les futurs mariés ✨</p>
                    </div>

                    <div class="flex flex-col items-center gap-8 w-full max-w-xs mx-auto animate-fade-up">
                        <div class="qr-card">
                            <img class="qr-image"
                                src="{{ $qrCodeDataUri ?? $qrCodeUrl }}"
                                alt="QR code vers l'invitation">
                            <p class="qr-caption">Scannez pour confirmer votre présence et découvrir plus de détails.
                            </p>
                        </div>
                        <div class="text-center flex flex-col items-center gap-2">
                            @if (session('download_error'))
                                <div class="alert alert-danger bg-red-50 border border-red-200 text-red-700 px-4 py-2 rounded-full">
                                    {{ session('download_error') }}
                                </div>
                            @endif
                            @if ($downloadNotice)
                                <div class="text-xs text-white/70 max-w-sm">{{ $downloadNotice }}</div>
                            @endif
                            <a href="{{ route('invitations.download', $guest->invitation_token) }}"
                                class="download-button relative inline-flex items-center justify-center gap-3 rounded-full bg-linear-to-r from-accent via-orange-400/80 to-amber-300 px-8 py-3 text-sm sm:text-base text-white font-semibold tracking-wide shadow-lg transition-transform hover:-translate-y-1 hover:shadow-glow focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-accent/70"
                                target="_blank" rel="noopener">
                                <span class="download-button__overlay"></span>
                                <span class="download-button__content">
                                    <span class="download-label">Télécharger l'invitation</span>
                                    <svg class="download-icon" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M7 17h10"></path>
                                        <path d="M12 3v11"></path>
                                        <path d="M8.5 12.5L12 16l3.5-3.5"></path>
                                    </svg>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="relative px-6 sm:px-10 lg:px-16 py-20 sm:py-24 bg-linear-to-br from-pearl via-white to-[#fdf1e8] rounded-[36px]">
            <div class="absolute inset-0 pointer-events-none opacity-60"
                style="background: radial-gradient(circle at 12% 18%, rgba(217,123,47,0.18), transparent 60%), radial-gradient(circle at 88% 88%, rgba(73,50,139,0.12), transparent 58%);">
            </div>
            <div class="relative max-w-6xl mx-auto space-y-12">
                <div class="text-center space-y-4 animate-fade-up">
                    <span
                        class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-full bg-ink/5 text-ink font-semibold tracking-[0.28em] uppercase text-xs sm:text-sm shadow-soft">Accès
                        &amp; Localisation</span>
                    <h2 class="font-display text-3xl sm:text-4xl text-accent">Retrouvez facilement nos lieux de fête</h2>
                    <p class="max-w-3xl mx-auto text-sm sm:text-base text-ink-soft leading-relaxed">
                        Deux moments inoubliables, deux adresses à ne pas manquer. Naviguez sur les cartes pour préparer
                        votre itinéraire vers l’église puis la salle de réception.
                    </p>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    <article class="map-card animate-fade-up">
                        <span class="map-card__badge">Cérémonie</span>
                        <h3 class="map-card__title">{{ $event['ceremony_title'] ?? 'Cérémonie' }}</h3>
                        <p class="map-card__address">{!! nl2br(e($event['ceremony_location'] ?? '')) !!}</p>
                        <div class="map-container" role="presentation">
                            @if (!empty($event['ceremony_map_query']))
                                <iframe
                                    src="https://www.google.com/maps?q={{ urlencode($event['ceremony_map_query']) }}&output=embed"
                                    loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"
                                    title="Carte de la cérémonie"></iframe>
                            @else
                                <span class="map-placeholder">Carte indisponible pour le moment</span>
                            @endif
                        </div>
                        @if (!empty($event['ceremony_map_query']))
                            <a class="map-actions"
                                href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($event['ceremony_map_query']) }}"
                                target="_blank" rel="noopener noreferrer">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"></path>
                                    <path
                                        d="M5.5 9.5c0 5.5 3.5 9.5 6.5 11.5 3-2 6.5-6 6.5-11.5a6.5 6.5 0 0 0-13 0Z">
                                    </path>
                                </svg>
                                Itinéraire
                            </a>
                        @endif
                    </article>
                    <article class="map-card animate-fade-up" style="animation-delay: 0.15s;">
                        <span class="map-card__badge">Réception</span>
                        <h3 class="map-card__title">{{ $event['reception_title'] ?? 'Réception' }}</h3>
                        <p class="map-card__address">{!! nl2br(e($event['reception_location'] ?? '')) !!}</p>
                        <div class="map-container" role="presentation">
                            @if (!empty($event['reception_map_query']))
                                <iframe
                                    src="https://www.google.com/maps?q={{ urlencode($event['reception_map_query']) }}&output=embed"
                                    loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"
                                    title="Carte de la réception"></iframe>
                            @else
                                <span class="map-placeholder">Carte indisponible pour le moment</span>
                            @endif
                        </div>
                        @if (!empty($event['reception_map_query']))
                            <a class="map-actions"
                                href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($event['reception_map_query']) }}"
                                target="_blank" rel="noopener noreferrer">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"></path>
                                    <path
                                        d="M5.5 9.5c0 5.5 3.5 9.5 6.5 11.5 3-2 6.5-6 6.5-11.5a6.5 6.5 0 0 0-13 0Z">
                                    </path>
                                </svg>
                                Itinéraire
                            </a>
                        @endif
                    </article>
                </div>
            </div>
        </section>

        <section
            class="relative px-6 sm:px-10 lg:px-16 py-20 sm:py-24 text-center text-ink-soft bg-cover bg-center overflow-hidden rounded-[36px]"
            style="background-image: linear-gradient(rgba(255,255,255,0.92), rgba(255,232,215,0.9)), url('https://images.unsplash.com/photo-1520854221057-9c5f6d0f6f07?auto=format&fit=crop&w=1400&q=80');">
            <div
                class="absolute inset-x-0 -bottom-32 h-64 bg-linear-to-t from-pearl to-transparent pointer-events-none">
            </div>
            <div class="relative max-w-5xl mx-auto space-y-5 sm:space-y-6 animate-fade-up">
                <h2 class="font-display text-3xl sm:text-4xl text-accent">Vos préférences</h2>
                <p class="text-base sm:text-lg leading-relaxed max-w-2xl mx-auto text-ink-soft">
                    Sélectionnez jusqu’à deux boissons qui vous ressemblent 🥂<br class="hidden sm:inline">
                    Elles seront enregistrées lors de la confirmation de votre présence.
                </p>
                <p class="italic text-xs sm:text-sm text-ink/60">(Deux suggestions au maximum)</p>
                <p class="text-sm text-ink-soft">
                    Vos choix actuels :
                    <strong id="selected-beverages-label">
                        {{ $selectedBeverageNames->isNotEmpty() ? $selectedBeverageNames->join(', ') : 'Aucune sélection' }}
                    </strong>
                </p>
            </div>

            <div class="relative mt-12 grid gap-12 sm:gap-14 max-w-5xl mx-auto">
                <div class="space-y-6">
                    <div class="flex flex-col items-center gap-3">
                        <h3 class="section-title text-sm tracking-[0.28em] uppercase text-ink font-semibold">Boissons
                            alcoolisées</h3>
                        <span class="h-0.5 w-14 rounded-full bg-linear-to-r from-accent to-amber-300"></span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 justify-items-center">
                        @foreach (($beverages['alcool'] ?? collect()) as $beverage)
                            @php $selected = $selectedBeverageIds->contains($beverage->id); @endphp
                            <button class="drink-option {{ $selected ? 'is-selected' : '' }}" type="button"
                                data-beverage-id="{{ $beverage->id }}" aria-pressed="{{ $selected ? 'true' : 'false' }}"
                                {{ $guest->rsvp_status === 'confirmed' ? 'disabled' : '' }}>
                                {{ $beverage->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="flex flex-col items-center gap-3">
                        <h3 class="section-title text-sm tracking-[0.28em] uppercase text-ink font-semibold">Boissons
                            non alcoolisées</h3>
                        <span class="h-0.5 w-14 rounded-full bg-linear-to-r from-accent to-amber-300"></span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 justify-items-center">
                        @foreach (($beverages['sucre'] ?? collect()) as $beverage)
                            @php $selected = $selectedBeverageIds->contains($beverage->id); @endphp
                            <button class="drink-option {{ $selected ? 'is-selected' : '' }}" type="button"
                                data-beverage-id="{{ $beverage->id }}" aria-pressed="{{ $selected ? 'true' : 'false' }}"
                                {{ $guest->rsvp_status === 'confirmed' ? 'disabled' : '' }}>
                                {{ $beverage->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="relative px-6 sm:px-10 lg:px-16 py-16 bg-white rounded-[36px]">
            <div class="max-w-3xl mx-auto text-center space-y-6">
                <h2 class="font-display text-3xl text-accent">Confirmation de présence</h2>
                @if (session('status'))
                    <div class="alert alert-success bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-full">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($guest->rsvp_status === 'confirmed')
                    <p class="text-ink-soft text-lg">Merci {{ $guest->display_name }} ! Votre présence est confirmée.
                        @if ($guest->rsvp_confirmed_at)
                            <br><span class="text-sm text-ink/60">Confirmé le {{ $guest->rsvp_confirmed_at->format('d/m/Y à H\hi') }}</span>
                        @endif
                    </p>
                @else
                    <p class="text-ink-soft text-lg">Merci de confirmer votre présence avant le grand jour pour nous
                        aider dans l’organisation.</p>
                    <form id="confirm-form" action="{{ route('invitations.confirm', $guest->invitation_token) }}" method="POST"
                        class="inline-flex flex-col items-center gap-4">
                        @csrf
                        <div id="preference-inputs"></div>
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-full bg-linear-to-r from-accent via-orange-400 to-amber-300 px-6 py-3 text-white font-semibold tracking-wide shadow-glow hover:-translate-y-0.5 transition-transform focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-accent/70">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12l5 5l10 -10"></path>
                    </svg>
                            Je confirme ma présence
                </button>
                    </form>
                @endif
            </div>
        </section>
    </main>
    <script>
        const qrDataUri = @js($qrCodeDataUri);
        const qrUrl = @js($qrCodeUrl);
        const pdfAssets = @json($pdfAssets);
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const drinkButtons = Array.from(document.querySelectorAll(".drink-option[data-beverage-id]"));
            const confirmForm = document.getElementById('confirm-form');
            const inputsContainer = document.getElementById('preference-inputs');
            const selectedLabel = document.getElementById('selected-beverages-label');
            const maxSelections = 2;
            const selectionOrder = @json($selectedBeverageIds->all());
            const beverageNames = @json($beverageMap);
            const isConfirmed = @json($guest->rsvp_status === 'confirmed');

            const updateSelectedLabel = () => {
                if (!selectedLabel) return;
                if (selectionOrder.length === 0) {
                    selectedLabel.textContent = 'Aucune sélection';
                    return;
                }
                selectedLabel.textContent = selectionOrder
                    .map((id) => beverageNames[id] ?? '')
                    .filter(Boolean)
                    .join(', ');
            };

            const syncPreferenceInputs = () => {
                if (!inputsContainer) return;
                inputsContainer.innerHTML = '';
                selectionOrder.forEach((id) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'beverage_ids[]';
                    input.value = id;
                    inputsContainer.appendChild(input);
                });
            };

            const setPressedState = (btn, pressed) => {
                btn.setAttribute("aria-pressed", pressed ? "true" : "false");
            };

            const unselectButton = (btn) => {
                const value = Number(btn.dataset.beverageId);
                if (!Number.isFinite(value)) return;
                const index = selectionOrder.indexOf(value);
                if (index !== -1) selectionOrder.splice(index, 1);
                btn.classList.remove("is-selected");
                setPressedState(btn, false);
            };

            const selectButton = (btn) => {
                const value = Number(btn.dataset.beverageId);
                if (!Number.isFinite(value)) return;
                if (selectionOrder.includes(value)) return;
                if (selectionOrder.length === maxSelections) {
                    const removedValue = selectionOrder.shift();
                    const removedBtn = drinkButtons.find((candidate) => Number(candidate.dataset.beverageId) === removedValue);
                    if (removedBtn) {
                        removedBtn.classList.remove("is-selected");
                        setPressedState(removedBtn, false);
                    }
                }
                selectionOrder.push(value);
                btn.classList.add("is-selected");
                setPressedState(btn, true);
            };

            drinkButtons.forEach((btn) => {
                setPressedState(btn, btn.classList.contains("is-selected"));
                btn.disabled = isConfirmed;
                btn.addEventListener("click", () => {
                    if (isConfirmed) {
                        return;
                    }
                    if (btn.classList.contains("is-selected")) {
                        unselectButton(btn);
                    } else {
                        selectButton(btn);
                    }
                    syncPreferenceInputs();
                    updateSelectedLabel();
                });
                btn.addEventListener("keydown", (event) => {
                    if (event.key === " " || event.key === "Enter") {
                        event.preventDefault();
                        btn.click();
                    }
                });
            });

            syncPreferenceInputs();
            updateSelectedLabel();

            if (confirmForm) {
                confirmForm.addEventListener('submit', () => {
                    syncPreferenceInputs();
                });
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Gestion de l'alerte de succès
            const successAlert = document.getElementById('success-alert');
            if (successAlert) {
                // Scroll vers l'alerte
                setTimeout(() => {
                    successAlert.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);

                // Fermeture automatique après 5 secondes
                setTimeout(() => {
                    successAlert.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                    successAlert.style.opacity = '0';
                    successAlert.style.transform = 'translateY(-100%)';
                    setTimeout(() => {
                        successAlert.remove();
                    }, 300);
                }, 5000);
            }

            const overlay = document.getElementById("reveal-overlay");
            const envelope = document.getElementById("envelope");
            const mainContent = document.querySelector("main");
            let hasOpened = false;

            // Arrêter la musique quand l'utilisateur quitte la page
            const stopAudio = () => {
                const audioEl = document.getElementById("invitation-audio");
                if (audioEl && !audioEl.paused) {
                    audioEl.pause();
                    audioEl.currentTime = 0;
                }
            };

            // Gestionnaire pour quand l'utilisateur quitte la page/ferme l'onglet
            window.addEventListener('beforeunload', stopAudio);

            // Gestionnaire pour quand l'utilisateur change d'onglet ou minimise la fenêtre
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    stopAudio();
                }
            });

            // Gestionnaire pour quand la page perd le focus
            window.addEventListener('blur', stopAudio);

            const openInvitation = () => {
                if (hasOpened || !overlay || !envelope || !mainContent) return;
                hasOpened = true;
                envelope.classList.add("open");
                overlay.classList.add("opening");

                const audioEl = document.getElementById("invitation-audio");
                if (audioEl) {
                    audioEl.volume = 0.65;
                    audioEl.play().catch(() => {
                        console.warn("Lecture audio bloquée. L'utilisateur devra interagir à nouveau pour la déclencher.");
                    });
                }

                setTimeout(() => {
                    mainContent.classList.add("pre-reveal");
                }, 1100);

                setTimeout(() => {
                    mainContent.classList.add("revealed");
                    mainContent.classList.remove("pre-reveal");
                    document.body.classList.remove("no-scroll");
                    window.dispatchEvent(new CustomEvent('invitation:revealed'));
                }, 2800);

                setTimeout(() => {
                    overlay.classList.add("hidden");
                }, 3600);

                setTimeout(() => {
                    overlay.style.display = "none";
                }, 4200);
            };

            if (overlay && envelope) {
                overlay.addEventListener("click", openInvitation);
                envelope.addEventListener("click", openInvitation);
                overlay.addEventListener("keyup", (event) => {
                    if (event.key === "Enter" || event.key === " ") {
                        openInvitation();
                    }
                });
                envelope.addEventListener("keyup", (event) => {
                    if (event.key === "Enter" || event.key === " ") {
                        openInvitation();
                    }
                });

                overlay.setAttribute("tabindex", "0");
                envelope.setAttribute("tabindex", "0");
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const snowLayer = document.querySelector(".snow-layer");
            if (snowLayer) {
                const createSnowFlake = () => {
                    const flake = document.createElement("span");
                    flake.className = "snow-flake";
                    const startX = Math.random() * 100;
                    const drift = (Math.random() - 0.5) * 120;
                    const duration = 12 + Math.random() * 14;
                    const delay = Math.random() * 4;
                    const size = 0.65 + Math.random() * 0.9;

                    flake.style.left = `${startX}vw`;
                    flake.style.setProperty("--x-offset", `${drift}px`);
                    flake.style.animationDuration = `${duration}s`;
                    flake.style.animationDelay = `${delay}s`;
                    flake.style.transform = `scale(${size})`;

                    flake.addEventListener("animationend", () => flake.remove());
                    snowLayer.appendChild(flake);
                };

                for (let i = 0; i < 40; i++) {
                    createSnowFlake();
                }

                setInterval(() => {
                    if (snowLayer.childElementCount < 120) {
                        createSnowFlake();
                    }
                }, 450);
            }
        });
    </script>
    <script>
        const initInvitationDownload = () => {
            if (window.__invitationDownloadInitialized) {
                return;
            }
            window.__invitationDownloadInitialized = true;

            console.debug("[Invitation] Script de téléchargement initialisé");
            const downloadBtn = document.getElementById("download-invitation");
            const downloadMessage = document.getElementById("download-message");
            if (!downloadBtn) {
                console.warn("[Invitation] Bouton de téléchargement introuvable dans le DOM");
                return;
            }

            console.debug("[Invitation] Bouton de téléchargement trouvé", downloadBtn);

            const showDownloadMessage = (text, type = 'info') => {
                if (!downloadMessage) return;
                downloadMessage.textContent = text;
                downloadMessage.className = type === 'error'
                    ? 'text-sm text-red-200'
                    : 'text-sm text-white/90';
            };

            const toDataUrl = async (url) => {
                const response = await fetch(url, { credentials: 'same-origin' });
                if (!response.ok) {
                    console.error("[Invitation][toDataUrl] Échec de chargement", url, response.status, response.statusText);
                    throw new Error(`Impossible de charger ${url}`);
                }

                const blob = await response.blob();
                return await new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = () => resolve(reader.result);
                    reader.onerror = (event) => {
                        console.error("[Invitation][toDataUrl] Lecture blob échouée", url, event);
                        reject(event);
                    };
                    reader.readAsDataURL(blob);
                });
            };

            if (window.location.protocol === "file:") {
                downloadBtn.addEventListener("click", () => {
                    alert("Pour générer le PDF, ouvrez cette page via un serveur local (ex. npx serve) ou uploadez-la en ligne. Les navigateurs bloquent l'accès aux fichiers locaux en mode file://");
                });
                downloadBtn.title = "Ouvrez la page via http:// pour activer le téléchargement";
                showDownloadMessage("Téléchargement indisponible en mode file://", 'error');
                return;
            }

            const setBusy = (busy) => {
                const labelEl = downloadBtn.querySelector(".download-label");
                const iconEl = downloadBtn.querySelector(".download-icon");
                const spinnerEl = downloadBtn.querySelector(".download-spinner");
                if (!downloadBtn.dataset.originalLabel && labelEl) {
                    downloadBtn.dataset.originalLabel = (labelEl.textContent || "").trim();
                }

                if (busy) {
                    downloadBtn.disabled = true;
                    downloadBtn.classList.add("opacity-80", "cursor-wait", "scale-[0.98]", "is-loading");
                    downloadBtn.setAttribute("aria-busy", "true");
                    if (labelEl) {
                        labelEl.textContent = "Génération en cours...";
                    }
                    if (iconEl) {
                        iconEl.setAttribute("aria-hidden", "true");
                    }
                    if (spinnerEl) {
                        spinnerEl.setAttribute("aria-hidden", "false");
                    }
                } else {
                    downloadBtn.disabled = false;
                    downloadBtn.classList.remove("opacity-80", "cursor-wait", "scale-[0.98]", "is-loading");
                    downloadBtn.removeAttribute("aria-busy");
                    if (labelEl) {
                        labelEl.textContent = downloadBtn.dataset.originalLabel || "Télécharger l'invitation";
                    }
                    if (iconEl) {
                        iconEl.removeAttribute("aria-hidden");
                    }
                    if (spinnerEl) {
                        spinnerEl.setAttribute("aria-hidden", "true");
                    }
                }
            };

            const loadAssets = () => {
                console.debug("[Invitation] Chargement des assets du PDF...");
                return Promise.resolve().then(async () => {
                    const fallbackQr = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8+B8AAwMCAO4P8LkAAAAASUVORK5CYII=';

                    const background = pdfAssets && pdfAssets.background
                        ? pdfAssets.background
                        : (pdfAssetUrls && pdfAssetUrls.background ? await toDataUrl(pdfAssetUrls.background) : null);
                    const bouquet = pdfAssets && pdfAssets.bouquet
                        ? pdfAssets.bouquet
                        : (pdfAssetUrls && pdfAssetUrls.bouquet ? await toDataUrl(pdfAssetUrls.bouquet) : null);

                    console.debug("[Invitation][loadAssets] Ressources chargées ?", {
                        background: Boolean(background),
                        bouquet: Boolean(bouquet),
                        qr: Boolean(qrDataUri),
                        viaDataUri: Boolean(pdfAssets && pdfAssets.background && pdfAssets.bouquet),
                        viaFetch: Boolean(!(pdfAssets && pdfAssets.background) && pdfAssetUrls && pdfAssetUrls.background),
                    });

                    if (!background || !bouquet) {
                        console.error("[Invitation][loadAssets] Ressources manquantes", { background, bouquet });
                        throw new Error("Ressources graphiques manquantes pour générer le PDF.");
                    }

                    const payload = {
                        background,
                        bouquet,
                        qr: qrDataUri || fallbackQr,
                    };
                    console.debug("[Invitation][loadAssets] Payload prêt", payload);
                    return payload;
                });
            };

            const ensureJsPdf = () => {
                if (window.jspdf && window.jspdf.jsPDF) {
                    console.debug("[Invitation] jsPDF déjà disponible");
                    return Promise.resolve(window.jspdf.jsPDF);
                }

                if (ensureJsPdf.promise) {
                    return ensureJsPdf.promise;
                }

                console.warn("[Invitation] jsPDF absent, tentative de chargement dynamique...");
                ensureJsPdf.promise = new Promise((resolve, reject) => {
                    const script = document.createElement("script");
                    script.src = "https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js";
                    script.async = true;
                    script.onload = () => {
                        if (window.jspdf && window.jspdf.jsPDF) {
                            console.debug("[Invitation] jsPDF chargé via fallback");
                            resolve(window.jspdf.jsPDF);
                        } else {
                            reject(new Error("jsPDF introuvable après chargement dynamique"));
                        }
                    };
                    script.onerror = (event) => reject(new Error("Échec de chargement de jsPDF depuis CDN"));
                    document.head.appendChild(script);
                });

                return ensureJsPdf.promise;
            };

            downloadBtn.addEventListener("click", async () => {
                console.info("[Invitation] Clic sur téléchargement");
                setBusy(true);
                showDownloadMessage('');
                try {
                    const results = await Promise.all([ensureJsPdf(), loadAssets()]);
                    const jsPDF = results[0];
                    const assets = results[1];
                    console.debug("[Invitation] jsPDF et assets prêts, génération du PDF", assets);
                    const doc = new jsPDF({ orientation: "portrait", unit: "mm", format: "a4" });

                    const pageWidth = doc.internal.pageSize.getWidth();
                    const pageHeight = doc.internal.pageSize.getHeight();

                    doc.addImage(assets.background, "JPEG", 0, 0, pageWidth, pageHeight, undefined, "FAST");

                    doc.setFillColor(255, 253, 249);
                    doc.roundedRect(14, 20, pageWidth - 28, pageHeight - 40, 10, 10, "F");

                    doc.setFillColor(240, 205, 133);
                    doc.roundedRect(20, 30, pageWidth - 40, 70, 14, 14, "F");

                    doc.addImage(assets.bouquet, "PNG", 24, 32, 45, 48, undefined, "FAST");
                    doc.addImage(assets.bouquet, "PNG", pageWidth - 68, pageHeight - 92, 44, 47, undefined, "FAST");

                    const centerText = (text, y, size = 16, font = "times", style = "normal") => {
                        doc.setFont(font, style);
                        doc.setFontSize(size);
                        doc.text(text, pageWidth / 2, y, { align: "center" });
                    };

                    const leftText = (text, x, y, size = 11, font = "helvetica", style = "normal") => {
                        doc.setFont(font, style);
                        doc.setFontSize(size);
                        doc.text(text, x, y);
                    };

                    doc.setTextColor(189, 137, 61);
                    centerText("Save the Date", 46, 13, "helvetica", "bold");

                    doc.setTextColor(204, 149, 69);
                    centerText({{ json_encode($event['couple_names'] ?? 'Nos mariés') }}, 66, 30, "times", "italic");

                    doc.setTextColor(71, 58, 48);
                    centerText("Mariage — 29 novembre 2025", 82, 13, "helvetica", "normal");

                    doc.setDrawColor(214, 170, 95);
                    doc.setLineWidth(0.3);
                    doc.line(50, 90, pageWidth - 50, 90);

                    doc.setTextColor(186, 132, 58);
                    centerText("Programme de la journée", 106, 12, "helvetica", "bold");

                    doc.setTextColor(204, 149, 69);
                    leftText("10h00", 32, 124, 13, "helvetica", "bold");

                    doc.setTextColor(71, 58, 48);
                    leftText("Bénédiction Nuptiale", 32, 134, 12, "times", "italic");
                    leftText("Église La Borne Cité verte", 32, 146);
                    leftText("12e rue", 32, 155, 10);
                    leftText("Réf: ex Promedis ou N6", 32, 164, 10);

                    doc.setTextColor(204, 149, 69);
                    leftText("19h00", pageWidth / 2 + 6, 124, 13, "helvetica", "bold");

                    doc.setTextColor(71, 58, 48);
                    leftText("Soirée Dansante", pageWidth / 2 + 6, 134, 12, "times", "italic");

                    leftText("Salle Malaïka", pageWidth / 2 + 6, 146);
                    leftText("C/ Ngaliema, route de Matadi, Q/ Météo", pageWidth / 2 + 6, 155, 10);
                    leftText("Réf: Regideso", pageWidth / 2 + 6, 164, 10);

                    doc.setTextColor(94, 75, 61);
                    centerText("Nous avons hâte de célébrer ce moment", 181, 10.5, "helvetica", "normal");
                    centerText("à vos côtés. Merci de confirmer votre présence.", 189, 10.5, "helvetica", "normal");

                    const qrSize = 46;
                    const qrX = pageWidth / 2 - qrSize / 2;
                    const qrY = 198;
                    doc.setDrawColor(243, 195, 74);
                    doc.setLineWidth(0.6);
                    doc.roundedRect(qrX - 7, qrY - 7, qrSize + 14, qrSize + 14, 8, 8, "S");
                    doc.addImage(assets.qr, "PNG", qrX, qrY, qrSize, qrSize, undefined, "FAST");

                    doc.setFont("helvetica", "italic");
                    doc.setFontSize(9.5);
                    doc.setTextColor(102, 78, 62);
                    doc.text("RSVP : {{ $invitationUrl }}", pageWidth / 2, qrY + qrSize + 16, { align: "center" });
                    doc.text("Dress code : {{ $event['dress_code'] ?? 'Chic et Élégant' }}", pageWidth / 2, qrY + qrSize + 23.5, { align: "center" });

                    doc.save({{ json_encode($event['pdf_filename'] ?? 'invitation.pdf') }});
                    console.info("[Invitation] PDF enregistré");
                    showDownloadMessage('Invitation téléchargée !');
                }
                catch (error) {
                    console.error("[Invitation] Erreur pendant la génération du PDF", error);
                    showDownloadMessage("Impossible de générer le PDF pour le moment. Veuillez réessayer.", 'error');
                } finally {
                    setBusy(false);
                }
            });

            console.debug("[Invitation] Gestionnaire de téléchargement prêt");
        };

        window.addEventListener("load", initInvitationDownload, { once: true });
        initInvitationDownload();
    </script>
</body>

</html>
