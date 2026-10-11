<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $metaTitle ?? 'Dibrugarh 24x7 - Festive Awards & Navigation' }}</title>

    <!-- SEO & Social Open Graph Tags -->
    <meta name="description" content="{{ $metaDescription ?? '' }}">
    <meta property="og:title" content="{{ $metaTitle ?? 'Dibrugarh 24x7 Festive Awards' }}">
    <meta property="og:description" content="{{ $metaDescription ?? '' }}">
    @if (!empty($ogImage))
        <meta property="og:image" content="{{ $ogImage }}">
    @endif
    <meta property="og:type" content="website">

    <!-- Google Fonts: Poppins & Playfair Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,700;1,500&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Festive Stylesheet -->
    <link rel="stylesheet" href="{{ asset('event-assets/css/style.css') }}">
</head>
<body>

    {{ $slot }}

    <!-- Bootstrap 5 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery 3.7.1 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @if (!empty($useAllPujasScript))
        <!-- All Pujas JavaScript Logic -->
        <script src="{{ asset('event-assets/js/allpujas.js') }}"></script>
    @else
        <!-- Festive Microsite Script -->
        <script src="{{ asset('event-assets/js/script.js') }}"></script>
    @endif

    {{ $scripts ?? '' }}
</body>
</html>
