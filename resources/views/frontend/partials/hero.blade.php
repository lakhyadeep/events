@php
    $heroBanner = $event?->banners?->first();
    $desktopBannerUrl = ($heroBanner && $heroBanner->desktop_image_url)
        ? $heroBanner->desktop_image_url
        : asset('event-assets/images/hero.png');
    $mobileBannerUrl = ($heroBanner && $heroBanner->mobile_image_url)
        ? $heroBanner->mobile_image_url
        : $desktopBannerUrl;
@endphp

<style>
    #home.hero-banner {
        background-image: linear-gradient(to right, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.45) 30%, rgba(0, 0, 0, 0) 100%), url('{{ $desktopBannerUrl }}');
    }
    @media (max-width: 991.98px) {
        #home.hero-banner {
            background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.15) 0%, rgba(0, 0, 0, 0.5) 55%, rgba(0, 0, 0, 0.9) 100%), url('{{ $mobileBannerUrl }}');
        }
    }
</style>

<section class="hero-banner text-start" id="home" style="--hero-desktop-bg: url('{{ $desktopBannerUrl }}'); --hero-mobile-bg: url('{{ $mobileBannerUrl }}');">
    <div class="container py-4">
        <div class="d-inline-block text-center">
            <img src="{{ asset('event-assets/images/logo-award2026.png') }}"
                alt="{{ $event->display_name ?? 'Dibrugarh Sharad Samman 2026' }}"
                class="img-fluid hero-logo-img mb-3">
            <p class="hero-tagline mb-0">
               {{ $event->tagline ?: ($event->display_name ?: 'Dib 24x7 Sharod Samman 2026') }}
            </p>
        </div>

        @if (isset($event) && ! $event->isVotingOpen())
            <div class="d-flex justify-content-center mt-4">
                <div class="voting-closed-banner p-3 rounded-4 shadow-sm text-center"
                     style="background: rgba(254, 242, 242, 0.95); border: 1px solid #fecaca; max-width: 480px; width: 100%; backdrop-filter: blur(8px);">
                    <div class="d-flex align-items-center justify-content-center gap-2 text-danger fw-bold fs-6 mb-1">
                        <i class="bi bi-lock-fill"></i>
                        <span>Voting is Closed</span>
                    </div>
                    <p class="mb-0 text-dark small">{{ $event->getVotingClosedMessage() }}</p>
                </div>
            </div>
        @endif
    </div>
</section>

