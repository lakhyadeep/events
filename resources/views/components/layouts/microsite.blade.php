@props([
    'event' => null,
    'metaTitle' => null,
    'metaDescription' => null,
    'ogImage' => null,
    'canonicalUrl' => null,
    'useAllPujasScript' => false,
    'useVoteScript' => false,
    'useSuccessScript' => false,
])
@php
    $currentEvent = $event ?? ($attributes->get('event') ?? null);
    if (! $currentEvent && class_exists(\App\Models\Event::class)) {
        $currentEvent = \App\Models\Event::where('status', 'active')->latest('year')->first() ?? \App\Models\Event::latest('year')->first();
    }
    $canonical = $canonicalUrl ?? url()->current();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $metaTitle ?? 'Dibrugarh 24x7 - Festive Awards & Navigation' }}</title>

    <!-- SEO & Social Open Graph Tags -->
    <link rel="canonical" href="{{ $canonical }}">
    <meta name="description" content="{{ $metaDescription ?? '' }}">
    <meta property="og:title" content="{{ $metaTitle ?? 'Dibrugarh 24x7 Festive Awards' }}">
    <meta property="og:description" content="{{ $metaDescription ?? '' }}">
    <meta property="og:url" content="{{ $canonical }}">
    @if (!empty($ogImage))
        <meta property="og:image" content="{{ $ogImage }}">
    @endif
    <meta property="og:type" content="website">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="otp-length" content="{{ \App\Models\OtpSetting::current()->otp_length }}">

    <!-- Google Fonts: Poppins & Playfair Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,700;1,500&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Festive Stylesheet -->
    <link rel="stylesheet" href="{{ asset('event-assets/css/style.css') }}?v={{ file_exists(public_path('event-assets/css/style.css')) ? filemtime(public_path('event-assets/css/style.css')) : '1.0' }}">
</head>
<body>

    {{ $slot }}

    <!-- Bootstrap 5 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery 3.7.1 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        window.apiBaseUrl = {!! json_encode(url('/api/v1')) !!};
        window.isVotingOpen = {{ ($currentEvent && $currentEvent->isVotingOpen()) ? 'true' : 'false' }};
        window.votingClosedMessage = {!! json_encode($currentEvent ? $currentEvent->getVotingClosedMessage() : 'Voting is currently closed for this event.') !!};
        window.votingCountdownTarget = {!! json_encode($currentEvent && $currentEvent->countdown_datetime ? $currentEvent->countdown_datetime->toIso8601String() : null) !!};
        window.homeUrl = {!! json_encode($currentEvent && $currentEvent->slug ? route('events.show', $currentEvent->slug) : route('home')) !!};
        window.allPujasUrl = {!! json_encode($currentEvent && $currentEvent->slug ? route('events.all-pujas', $currentEvent->slug) : route('all-pujas')) !!};
        window.voteBaseUrl = {!! json_encode($currentEvent && $currentEvent->slug ? route('events.vote', $currentEvent->slug) : route('vote')) !!};
        window.voteSuccessUrl = {!! json_encode($currentEvent && $currentEvent->slug ? route('events.vote-success', $currentEvent->slug) : route('vote-success')) !!};
    </script>
    @if (!empty($useAllPujasScript))
        <!-- All Pujas JavaScript Logic -->
        <script src="{{ asset('event-assets/js/allpujas.js') }}?v={{ file_exists(public_path('event-assets/js/allpujas.js')) ? filemtime(public_path('event-assets/js/allpujas.js')) : '1.0' }}"></script>
    @elseif (!empty($useVoteScript))
        <!-- Vote Page Logic -->
        <script src="{{ asset('event-assets/js/vote.js') }}?v={{ file_exists(public_path('event-assets/js/vote.js')) ? filemtime(public_path('event-assets/js/vote.js')) : '1.0' }}"></script>
    @elseif (!empty($useSuccessScript))
        <!-- Vote Success Page Logic -->
        <script src="{{ asset('event-assets/js/success.js') }}?v={{ file_exists(public_path('event-assets/js/success.js')) ? filemtime(public_path('event-assets/js/success.js')) : '1.0' }}"></script>
    @else
        <!-- Festive Microsite Script -->
        <script src="{{ asset('event-assets/js/script.js') }}?v={{ file_exists(public_path('event-assets/js/script.js')) ? filemtime(public_path('event-assets/js/script.js')) : '1.0' }}"></script>
    @endif

    {{ $scripts ?? '' }}
</body>
</html>
