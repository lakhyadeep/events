<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $metaTitle ?? 'Dib 24x7 Event Microsite' }}</title>

    <!-- SEO & Social Open Graph Tags -->
    <meta name="description" content="{{ $metaDescription ?? '' }}">
    <meta property="og:title" content="{{ $metaTitle ?? 'Dib 24x7 Event' }}">
    <meta property="og:description" content="{{ $metaDescription ?? '' }}">
    @if (!empty($ogImage))
        <meta property="og:image" content="{{ $ogImage }}">
    @endif
    <meta property="og:type" content="website">

    <!-- Fonts: Figtree / Outfit -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS (Modern CDN for seamless runtime rendering) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Figtree', 'sans-serif'],
                    },
                    colors: {
                        zinc: {
                            850: '#1e1e22',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js Collapse plugin & Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>

    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 font-sans antialiased selection:bg-amber-500 selection:text-black min-h-screen flex flex-col">

    {{ $slot }}

    @livewireScripts
</body>
</html>
