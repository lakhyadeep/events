<section class="hero-banner text-start" id="home">
    <div class="container py-4">
        <div class="d-inline-block text-center">
            @php
                $heroBanner = $event->banners->first();
            @endphp
            @if ($heroBanner && $heroBanner->desktop_image_url)
                <img src="{{ $heroBanner->desktop_image_url }}" alt="{{ $heroBanner->title ?: ($event->display_name ?? 'Dibrugarh Sharad Samman') }}"
                    class="img-fluid hero-logo-img mb-3">
            @else
                <img src="{{ asset('event-assets/images/logo-award2026.png') }}" alt="{{ $event->display_name ?? 'Dibrugarh Sharad Samman 2026' }}"
                    class="img-fluid hero-logo-img mb-3">
            @endif
            <p class="hero-tagline mb-0">
               {{ $event->display_name ?: ($event->tagline ?: 'Dib 24x7 Sharod Samman 2026') }}
            </p>
        </div>
    </div>
</section>
