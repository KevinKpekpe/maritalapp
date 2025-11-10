<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mariage Raphael et Daniella</title>
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
        }

        .bouquet-deco--left {
            top: -60px;
            left: -70px;
            transform: rotate(-8deg);
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

<body class="overflow-x-hidden no-scroll">
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
                <p class="envelope__initials">R &amp; D</p>
                <em>Raphael &amp; Daniella</em>
                <small>29 novembre 2025</small>
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
                    Raphael et Daniella</h1>

                <div class="relative flex items-center justify-center">
                    <div class="absolute -inset-4 rounded-full bg-gradient-to-r from-white/10 to-white/0 blur-2xl">
                    </div>
                    <img src="{{ asset('invitations/savethedate.png') }}" alt="Save the date"
                        class="relative w-48 sm:w-64 lg:w-[22rem] opacity-95 drop-shadow-2xl">
                </div>
            </div>
        </section>

        <section class="relative px-6 sm:px-10 lg:px-16 py-20 sm:py-24 bg-no-repeat bg-cover"
            style="background-image: linear-gradient(rgba(255,247,244,0.95), rgba(255,233,224,0.88)), url('https://images.unsplash.com/photo-1486427944299-d1955d23e34d?auto=format&fit=crop&w=1400&q=80');">
            <div
                class="relative ornate-card max-w-5xl mx-auto px-7 sm:px-12 lg:px-16 py-14 sm:py-16 shadow-2xl border border-white/20">
                <img src="{{ asset('invitations/bouquet.png') }}" alt="Décoration bouquet"
                    class="bouquet-deco bouquet-deco--left hidden md:block">
                <img src="{{ asset('invitations/bouquet.png') }}" alt="Décoration bouquet"
                    class="bouquet-deco bouquet-deco--right hidden md:block">
                <div class="flex flex-col lg:flex-row items-center lg:items-start gap-12 lg:gap-14">
                    <div class="flex-1 space-y-6 lg:space-y-8 text-center lg:text-left animate-fade-up">
                        <div class="space-y-4">
                            <span
                                class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-full bg-accent/10 text-accent font-semibold tracking-[0.28em] uppercase text-xs sm:text-sm shadow-soft">La
                                célébration</span>
                            <div class="space-y-2">
                                <h2 class="font-display text-4xl sm:text-5xl text-accent leading-tight">Raphael &amp;
                                    Daniella</h2>
                                <p
                                    class="font-serif text-sm sm:text-base tracking-[0.26em] uppercase text-ink font-medium">
                                    Samedi 29 novembre 2025</p>
                            </div>
                            <div class="divider text-ink-soft">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#D97B2F"
                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 7l-5 5 5 5"></path>
                                    <path d="M17 7l-5 5 5 5"></path>
                                </svg>
                                <span class="uppercase tracking-[0.32em] text-xs sm:text-sm text-ink font-medium">Unir
                                    nos destins</span>
                            </div>
                        </div>
                        <p
                            class="text-base sm:text-lg lg:text-xl text-ink-soft leading-relaxed max-w-xl mx-auto lg:mx-0">
                            C’est avec une immense joie que nous vous invitons à témoigner de nos vœux sacrés 💍 et à
                            partager une soirée scintillante ✨. Votre présence chérira notre histoire et illuminera
                            cette journée.
                        </p>

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
                                    <span class="schedule-time">10h00</span>
                                    <h3>Bénédiction nuptiale</h3>
                                    <p>
                                        Église La Borne Cité verte, 13e rue cité verte.<br>
                                        <span class="schedule-highlight">Réf. École Pierre Bouvet</span>
                                    </p>
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
                                    <span class="schedule-time">19h30</span>
                                    <h3>Soirée dansante</h3>
                                    <p>
                                        Salle de fête Sanam Center, 13B avenue Basoko – Gombe.<br>
                                        <span class="schedule-highlight">Réf. Orca</span>
                                    </p>
                                </div>
                            </article>
                        </div>

                        <p class="pt-2 font-semibold text-ink uppercase tracking-[0.24em] text-xs sm:text-sm">
                            Cordialement les futurs mariés ✨</p>
                    </div>

                    <div class="flex flex-col items-center gap-8 w-full max-w-xs mx-auto animate-fade-up">
                        <div class="qr-card">
                            <img class="qr-image"
                                src="https://api.qrserver.com/v1/create-qr-code/?size=380x380&data=https%3A%2F%2Fplanningevents-rdc.com%2Finvitation%2Fralph-daniella"
                                alt="QR code vers l'invitation">
                            <p class="qr-caption">Scannez pour confirmer votre présence et découvrir plus de détails.
                            </p>
                        </div>
                        <button id="download-invitation"
                            class="download-button relative inline-flex items-center justify-center gap-3 rounded-full bg-gradient-to-r from-accent via-orange-400/80 to-amber-300 px-8 py-3 text-sm sm:text-base text-white font-semibold tracking-wide shadow-lg transition-transform hover:-translate-y-1 hover:shadow-glow focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-accent/70"
                            type="button">
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
                                <span class="download-spinner" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                        stroke-linecap="round">
                                        <path d="M12 3a9 9 0 1 1-9 9" stroke-opacity="0.35"></path>
                                        <path d="M21 12a9 9 0 0 0-9-9" stroke-linecap="round"></path>
                                    </svg>
                                </span>
                            </span>
                        </button>
                        <span class="text-xs text-ink-soft text-center">Recevez le programme complet et vos accès
                            personnalisés.</span>
                    </div>
                </div>
            </div>
        </section>

        <section
            class="relative px-6 sm:px-10 lg:px-16 py-20 sm:py-24 text-center text-ink-soft bg-cover bg-center overflow-hidden rounded-[36px]"
            style="background-image: linear-gradient(rgba(255,255,255,0.92), rgba(255,232,215,0.9)), url('https://images.unsplash.com/photo-1520854221057-9c5f6d0f6f07?auto=format&fit=crop&w=1400&q=80');">
            <div
                class="absolute inset-x-0 -bottom-32 h-64 bg-gradient-to-t from-pearl to-transparent pointer-events-none">
            </div>
            <div class="relative max-w-5xl mx-auto space-y-5 sm:space-y-6 animate-fade-up">
                <h2 class="font-display text-3xl sm:text-4xl text-accent">Vos préférences</h2>
                <p class="text-base sm:text-lg leading-relaxed max-w-2xl mx-auto text-ink-soft">
                    Aidez-nous à préparer une carte des boissons qui vous ressemble 🥂<br class="hidden sm:inline">
                    Sélectionnez jusqu’à deux options pour guider nos choix.
                </p>
                <p class="italic text-xs sm:text-sm text-ink/60">(Deux suggestions au maximum)</p>
            </div>

            <div class="relative mt-12 grid gap-12 sm:gap-14 max-w-5xl mx-auto">
                <div class="space-y-6">
                    <div class="flex flex-col items-center gap-3">
                        <h3 class="section-title text-sm tracking-[0.28em] uppercase text-ink font-semibold">Boissons
                            alcoolisées</h3>
                        <span class="h-0.5 w-14 rounded-full bg-gradient-to-r from-accent to-amber-300"></span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 justify-items-center">
                        <button class="drink-option" type="button" data-drink="castel">Castel</button>
                        <button class="drink-option" type="button" data-drink="beaufort">Beaufort</button>
                        <button class="drink-option" type="button" data-drink="tembo">Tembo</button>
                        <button class="drink-option" type="button" data-drink="heineken">Heinekein</button>
                        <button class="drink-option" type="button" data-drink="nkoyi">Nkoyi</button>
                        <button class="drink-option" type="button" data-drink="likofi">Likofi</button>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="flex flex-col items-center gap-3">
                        <h3 class="section-title text-sm tracking-[0.28em] uppercase text-ink font-semibold">Boissons
                            non alcoolisées</h3>
                        <span class="h-0.5 w-14 rounded-full bg-gradient-to-r from-accent to-amber-300"></span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 justify-items-center">
                        <button class="drink-option" type="button" data-drink="coca">Coca</button>
                        <button class="drink-option" type="button" data-drink="fanta">Fanta</button>
                        <button class="drink-option" type="button" data-drink="vitalo">Vitalo</button>
                        <button class="drink-option" type="button" data-drink="malta">Malta</button>
                        <button class="drink-option" type="button" data-drink="sprite">Sprite</button>
                        <button class="drink-option" type="button" data-drink="energy-malt">Energy Malt</button>
                    </div>
                </div>
            </div>
        </section>

        <section
            class="relative px-6 sm:px-10 lg:px-16 py-24 flex items-center justify-center bg-cover bg-center rounded-[36px]"
            style="background-image: linear-gradient(rgba(30,26,26,0.58), rgba(30,26,26,0.55)), url('{{ asset('invitations/bg.jpeg') }}');">
            <div class="absolute inset-0 grain-overlay opacity-60"></div>
            <div
                class="relative max-w-lg w-full bg-white/95 backdrop-blur-xl rounded-[32px] px-6 sm:px-8 lg:px-10 py-12 sm:py-14 text-center shadow-2xl animate-fade-up border border-white/60">
                <h2 class="font-display text-3xl sm:text-4xl text-accent">Livre d'or</h2>
                <p class="mt-4 text-sm sm:text-base text-ink-soft">Une pensée, un vœu, un souvenir… laissez un mot aux
                    mariés.</p>
                <label
                    class="text-left w-full block font-semibold text-ink mt-10 mb-4 uppercase text-xs tracking-[0.24em]"
                    for="message">Votre message</label>
                <textarea
                    class="w-full min-h-[150px] sm:min-h-[170px] rounded-3xl border border-black/5 bg-white/90 px-5 sm:px-6 py-4 text-left text-sm sm:text-base text-ink shadow-inner focus:outline-none focus:ring-2 focus:ring-accent/40 resize-y transition"
                    id="message" placeholder="Écrivez votre message ici..."></textarea>
                <button
                    class="mt-8 inline-flex items-center justify-center gap-2 rounded-full bg-gradient-to-r from-accent via-amber-400 to-orange-300 px-7 sm:px-9 py-3 text-white font-semibold tracking-wide shadow-glow hover:-translate-y-0.5 transition-transform"
                    type="button">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16v12H5.17L4 17.17z"></path>
                        <path d="M8 8h8"></path>
                        <path d="M8 12h5"></path>
                    </svg>
                    Envoyer
                </button>
            </div>
        </section>
    </main>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const drinkButtons = Array.from(document.querySelectorAll(".drink-option[data-drink]"));
            const maxSelections = 2;
            const selectionOrder = [];

            const setPressedState = (btn, pressed) => {
                btn.setAttribute("aria-pressed", pressed ? "true" : "false");
            };

            const unselectButton = (btn) => {
                const value = btn.dataset.drink;
                if (!value) return;
                const index = selectionOrder.indexOf(value);
                if (index !== -1) selectionOrder.splice(index, 1);
                btn.classList.remove("is-selected");
                setPressedState(btn, false);
            };

            const selectButton = (btn) => {
                const value = btn.dataset.drink;
                if (!value) return;
                if (selectionOrder.includes(value)) return;
                if (selectionOrder.length === maxSelections) {
                    const removedValue = selectionOrder.shift();
                    const removedBtn = drinkButtons.find((candidate) => candidate.dataset.drink === removedValue);
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
                btn.addEventListener("click", () => {
                    if (btn.classList.contains("is-selected")) {
                        unselectButton(btn);
                    } else {
                        selectButton(btn);
                    }
                });
                btn.addEventListener("keydown", (event) => {
                    if (event.key === " " || event.key === "Enter") {
                        event.preventDefault();
                        btn.click();
                    }
                });
            });

            const downloadBtn = document.getElementById("download-invitation");
            if (!downloadBtn) {
                return;
            }

            if (window.location.protocol === "file:") {
                downloadBtn.addEventListener("click", () => {
                    alert("Pour générer le PDF, ouvrez cette page via un serveur local (ex. npx serve) ou uploadez-la en ligne. Les navigateurs bloquent l'accès aux fichiers locaux en mode file://");
                });
                downloadBtn.title = "Ouvrez la page via http:// pour activer le téléchargement";
                return;
            }

            let assetsPromise;
            const toDataUrl = async (url) => {
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`Impossible de charger ${url}`);
                }
                const blob = await response.blob();
                return await new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = () => resolve(reader.result);
                    reader.onerror = reject;
                    reader.readAsDataURL(blob);
                });
            };

            const loadAssets = () => {
                if (!assetsPromise) {
                    assetsPromise = Promise.all([
                        toDataUrl("{{ asset('invitations/fond.jpeg') }}"),
                        toDataUrl("{{ asset('invitations/bouquet.png') }}"),
                        toDataUrl("https://api.qrserver.com/v1/create-qr-code/?size=360x360&data=https%3A%2F%2Fplanningevents-rdc.com%2Finvitation%2Fralph-daniella")
                    ]).then(([background, bouquet, qr]) => ({ background, bouquet, qr }));
                }
                return assetsPromise;
            };

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

            const initialisePdfDownload = () => {
                if (!window.jspdf || !window.jspdf.jsPDF || downloadBtn.dataset.pdfReady === "true") {
                    return Boolean(downloadBtn.dataset.pdfReady === "true");
                }

                downloadBtn.dataset.pdfReady = "true";
                const { jsPDF } = window.jspdf;

                downloadBtn.addEventListener("click", async () => {
                    setBusy(true);
                    try {
                        const assets = await loadAssets();
                        const doc = new jsPDF({ orientation: "portrait", unit: "mm", format: "a4" });
                        const pageWidth = doc.internal.pageSize.getWidth();
                        const pageHeight = doc.internal.pageSize.getHeight();

                        // --- Image de fond ---
                        doc.addImage(assets.background, "JPEG", 0, 0, pageWidth, pageHeight, undefined, "FAST");

                        // --- Bloc principal (fond ivoire clair, sans alpha) ---
                        doc.setFillColor(255, 253, 249);
                        doc.roundedRect(14, 20, pageWidth - 28, pageHeight - 40, 10, 10, "F");

                        // --- Bande décorative dorée pâle ---
                        doc.setFillColor(240, 205, 133);
                        doc.roundedRect(20, 30, pageWidth - 40, 70, 14, 14, "F");

                        // --- Images florales ---
                        doc.addImage(assets.bouquet, "PNG", 24, 32, 45, 48, undefined, "FAST");
                        doc.addImage(assets.bouquet, "PNG", pageWidth - 68, pageHeight - 92, 44, 47, undefined, "FAST");

                        // --- Fonctions utilitaires pour centrer / aligner le texte ---
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

                        // --- Titres principaux ---
                        doc.setTextColor(189, 137, 61);
                        centerText("Save the Date", 46, 13, "helvetica", "bold");

                        doc.setTextColor(204, 149, 69);
                        centerText("Raphael & Daniella", 66, 30, "times", "italic");

                        doc.setTextColor(71, 58, 48);
                        centerText("Mariage — 29 novembre 2025", 82, 13, "helvetica", "normal");

                        // --- Ligne décorative ---
                        doc.setDrawColor(214, 170, 95);
                        doc.setLineWidth(0.3);
                        doc.line(50, 90, pageWidth - 50, 90);

                        // --- Programme ---
                        doc.setTextColor(186, 132, 58);
                        centerText("Programme de la journée", 106, 12, "helvetica", "bold");

                        doc.setTextColor(204, 149, 69);
                        leftText("10h00", 32, 124, 13, "helvetica", "bold");

                        doc.setTextColor(71, 58, 48);
                        leftText("Bénédiction Nuptiale", 32, 134, 12, "times", "italic");
                        leftText("Église La Borne Cité verte", 32, 146);
                        leftText("13e rue cité verte (Réf. École Pierre Bouvet)", 32, 155, 10);

                        doc.setTextColor(204, 149, 69);
                        leftText("19h30", pageWidth / 2 + 6, 124, 13, "helvetica", "bold");

                        doc.setTextColor(71, 58, 48);
                        leftText("Soirée Dansante", pageWidth / 2 + 6, 134, 12, "times", "italic");
                        leftText("Salle de fête Sanam Center", pageWidth / 2 + 6, 146);
                        leftText("13B avenue Basoko – Gombe (Réf. Orca)", pageWidth / 2 + 6, 155, 10);

                        // --- Texte de conclusion ---
                        doc.setTextColor(94, 75, 61);
                        centerText("Nous avons hâte de célébrer ce moment", 181, 10.5, "helvetica", "normal");
                        centerText("à vos côtés. Merci de confirmer votre présence.", 189, 10.5, "helvetica", "normal");

                        // --- QR Code et encadrement doré ---
                        const qrSize = 46;
                        const qrX = pageWidth / 2 - qrSize / 2;
                        const qrY = 198;
                        doc.setDrawColor(243, 195, 74);
                        doc.setLineWidth(0.6);
                        doc.roundedRect(qrX - 7, qrY - 7, qrSize + 14, qrSize + 14, 8, 8, "S");
                        doc.addImage(assets.qr, "PNG", qrX, qrY, qrSize, qrSize, undefined, "FAST");

                        // --- Infos additionnelles ---
                        doc.setFont("helvetica", "italic");
                        doc.setFontSize(9.5);
                        doc.setTextColor(102, 78, 62);
                        doc.text("RSVP : planningevents-rdc.com/invitation/ralph-daniella", pageWidth / 2, qrY + qrSize + 16, { align: "center" });
                        doc.text("Dress code : Chic et nuances chaleureuses", pageWidth / 2, qrY + qrSize + 23.5, { align: "center" });

                        // --- Sauvegarde ---
                        doc.save("Invitation-Raphael-Daniella.pdf");

                    }
                    catch (error) {
                        console.error(error);
                        alert("Impossible de générer le PDF pour le moment. Veuillez réessayer.");
                    } finally {
                        setBusy(false);
                    }
                });

                return true;
            };

            const waitForJsPdf = (attempt = 0) => {
                if (initialisePdfDownload()) {
                    return;
                }
                if (attempt >= 60) {
                    console.error("jsPDF n'a pas pu être chargé.");
                    downloadBtn.addEventListener("click", () => {
                        alert("Le générateur PDF ne s'est pas chargé correctement. Vérifiez votre connexion puis actualisez la page.");
                    }, { once: true });
                    return;
                }
                setTimeout(() => waitForJsPdf(attempt + 1), 100);
            };

            waitForJsPdf();
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const overlay = document.getElementById("reveal-overlay");
            const envelope = document.getElementById("envelope");
            const mainContent = document.querySelector("main");
            let hasOpened = false;

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
</body>

</html>
