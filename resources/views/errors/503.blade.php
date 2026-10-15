@php
    $appTitle = config('app.name', 'Dibrugarh 24x7');
    $defaultMessage = 'Our platform is currently undergoing scheduled maintenance and updates to improve your experience. All services and features will be back online shortly.';
    $rawMessage = isset($exception) && filled($exception->getMessage()) ? trim($exception->getMessage()) : null;
    $maintenanceMessage = ($rawMessage && $rawMessage !== 'Service Unavailable') ? $rawMessage : $defaultMessage;

    $retrySeconds = null;
    if (isset($retryAfter) && is_numeric($retryAfter) && $retryAfter > 0) {
        $retrySeconds = (int) $retryAfter;
    } elseif (isset($exception) && method_exists($exception, 'getHeaders')) {
        $headers = $exception->getHeaders();
        if (!empty($headers['Retry-After']) && is_numeric($headers['Retry-After'])) {
            $retrySeconds = (int) $headers['Retry-After'];
        }
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>System Under Maintenance | {{ $appTitle }}</title>

    @if ($retrySeconds)
        <meta http-equiv="refresh" content="{{ $retrySeconds }}">
    @endif

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,700;1,500&display=swap" rel="stylesheet">

    <style>
        :root {
            --festive-bg: #fdf8f2;
            --festive-cream: #faf3eb;
            --crimson-red: #b70d18;
            --crimson-dark: #8c0c16;
            --brand-yellow: #ffcc00;
            --gold-accent: #d4a373;
            --gold-border: rgba(212, 163, 115, 0.35);
            --text-dark: #231f20;
            --text-muted: #64748b;
            --card-bg: #ffffff;
            --card-shadow: 0 20px 45px -10px rgba(183, 13, 24, 0.08), 0 10px 25px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--festive-bg);
            background-image:
                radial-gradient(ellipse at 10% 15%, rgba(212, 163, 115, 0.15) 0%, transparent 45%),
                radial-gradient(ellipse at 90% 85%, rgba(183, 13, 24, 0.07) 0%, transparent 50%),
                linear-gradient(to bottom, #fffdfa, #fdf8f2);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.6;
        }

        /* Top Maintenance Banner */
        .maintenance-banner {
            position: sticky;
            top: 0;
            z-index: 100;
            background: linear-gradient(90deg, #8c0c16 0%, #b70d18 50%, #9b0b14 100%);
            color: #ffffff;
            padding: 10px 16px;
            box-shadow: 0 4px 14px rgba(140, 12, 22, 0.25);
            border-bottom: 2px solid var(--brand-yellow);
        }

        .banner-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            font-size: 0.925rem;
            text-align: center;
            flex-wrap: wrap;
        }

        .banner-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background-color: rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 204, 0, 0.45);
            color: var(--brand-yellow);
            padding: 3px 10px;
            border-radius: 9999px;
            font-size: 0.775rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--brand-yellow);
            box-shadow: 0 0 0 rgba(255, 204, 0, 0.7);
            animation: pulse-animation 1.8s infinite;
        }

        @keyframes pulse-animation {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(255, 204, 0, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 8px rgba(255, 204, 0, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(255, 204, 0, 0);
            }
        }

        .banner-text {
            font-weight: 500;
            letter-spacing: 0.01em;
        }

        /* Main Content Layout */
        .content-wrap {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .maintenance-card {
            background: var(--card-bg);
            border: 1px solid var(--gold-border);
            border-radius: 24px;
            box-shadow: var(--card-shadow);
            max-width: 620px;
            width: 100%;
            padding: 48px 36px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .maintenance-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--gold-accent), var(--crimson-red), var(--brand-yellow));
        }

        .brand-logo-wrap {
            margin-bottom: 24px;
        }

        .brand-logo-wrap img {
            max-height: 52px;
            max-width: 180px;
            object-fit: contain;
            height: auto;
        }

        .icon-circle {
            width: 86px;
            height: 86px;
            margin: 0 auto 24px;
            border-radius: 50%;
            background: linear-gradient(135deg, #fff2e0 0%, #fbe7b7 100%);
            border: 2px solid var(--gold-border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--crimson-dark);
            box-shadow: 0 10px 20px rgba(212, 163, 115, 0.2);
            position: relative;
        }

        .icon-circle svg {
            width: 42px;
            height: 42px;
            animation: gentle-spin 12s linear infinite;
        }

        @keyframes gentle-spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--crimson-dark);
            margin-bottom: 12px;
            line-height: 1.25;
        }

        .card-description {
            color: var(--text-muted);
            font-size: 1rem;
            margin-bottom: 28px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .notice-callout {
            background-color: var(--festive-cream);
            border-left: 4px solid var(--crimson-red);
            border-radius: 8px;
            padding: 14px 18px;
            text-align: left;
            margin-bottom: 28px;
            color: var(--text-dark);
            font-size: 0.925rem;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .notice-callout svg {
            width: 20px;
            height: 20px;
            color: var(--crimson-red);
            flex-shrink: 0;
            margin-top: 2px;
        }

        .countdown-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-bottom: 24px;
            background: #f8fafc;
            padding: 6px 14px;
            border-radius: 9999px;
            border: 1px solid #e2e8f0;
        }

        .actions-wrap {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-refresh {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--crimson-red) 0%, var(--crimson-hover, #9b0b14) 100%);
            color: #ffffff;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 12px 28px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(183, 13, 24, 0.25);
            transition: all 0.25s ease;
            text-decoration: none;
        }

        .btn-refresh:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(183, 13, 24, 0.35);
            background: #8c0c16;
        }

        .btn-refresh:active {
            transform: translateY(0);
        }

        .btn-refresh svg {
            width: 18px;
            height: 18px;
            transition: transform 0.4s ease;
        }

        .btn-refresh.refreshing svg {
            animation: spin-once 0.8s ease-in-out infinite;
        }

        @keyframes spin-once {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Footer info */
        .site-footer {
            padding: 18px 20px;
            text-align: center;
            font-size: 0.825rem;
            color: var(--text-muted);
            border-top: 1px solid var(--gold-border);
            background-color: var(--festive-cream);
        }

        @media (max-width: 640px) {
            .maintenance-card {
                padding: 32px 20px;
                border-radius: 18px;
            }

            .card-title {
                font-size: 1.6rem;
            }

            .banner-container {
                font-size: 0.85rem;
                gap: 8px;
            }

            .btn-refresh {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- Simple Under Maintenance Top Banner -->
    <aside class="maintenance-banner" role="alert" aria-live="polite">
        <div class="banner-container">
            <span class="banner-pill">
                <span class="pulse-dot"></span>
                System Notice
            </span>
            <span class="banner-text">
                The platform is currently under maintenance. We will be back online shortly.
            </span>
        </div>
    </aside>

    <!-- Main Content Presentation -->
    <main class="content-wrap">
        <div class="maintenance-card">
            <!-- Brand Logo -->
            <div class="brand-logo-wrap">
                <img src="{{ asset('event-assets/images/logo-dibru.png') }}"
                     alt="{{ $appTitle }}"
                     onerror="this.style.display='none'">
            </div>

            <!-- Maintenance Icon with subtle rotary animation -->
            <div class="icon-circle" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
            </div>

            <!-- Headings -->
            <h1 class="card-title">We'll Be Right Back!</h1>
            <p class="card-description">
                {{ $maintenanceMessage }}
            </p>

            @if ($retrySeconds)
                <div class="countdown-indicator" id="countdownIndicator">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span>Auto-checking in <strong id="countdownSecs">{{ $retrySeconds }}</strong>s</span>
                </div>
            @endif

            <!-- Action Button -->
            <div class="actions-wrap">
                <button type="button" class="btn-refresh" id="refreshBtn" onclick="refreshPage()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                    </svg>
                    <span>Check Again</span>
                </button>
            </div>
        </div>
    </main>

    <!-- Simple Footer -->
    <footer class="site-footer">
        &copy; {{ date('Y') }} {{ $appTitle }}. All rights reserved. Essential system maintenance in progress.
    </footer>

    <script>
        function refreshPage() {
            var btn = document.getElementById('refreshBtn');
            if (btn) {
                btn.classList.add('refreshing');
                btn.setAttribute('disabled', 'disabled');
            }
            window.location.reload();
        }

        @if ($retrySeconds)
            (function() {
                var secondsLeft = {{ $retrySeconds }};
                var elem = document.getElementById('countdownSecs');
                var timer = setInterval(function() {
                    secondsLeft--;
                    if (elem) {
                        elem.textContent = secondsLeft;
                    }
                    if (secondsLeft <= 0) {
                        clearInterval(timer);
                        refreshPage();
                    }
                }, 1000);
            })();
        @endif
    </script>
</body>
</html>
