<!-- Marketing Promotional Banner Section (Auto-Rotating Carousel) -->
<section class="marketing-banner-section py-4" id="promo-banner">
    <div class="container">
        <div class="marketing-banner-wrapper rounded-3 overflow-hidden shadow-sm position-relative">
            <div id="marketingBannerCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel"
                data-bs-interval="4500" data-bs-pause="hover" data-bs-touch="true">
                <!-- Carousel Indicators -->
                <div class="carousel-indicators marketing-carousel-indicators mb-2">
                    <button type="button" data-bs-target="#marketingBannerCarousel" data-bs-slide-to="0"
                        class="active" aria-current="true" aria-label="Banner Ad 1"></button>
                    <button type="button" data-bs-target="#marketingBannerCarousel" data-bs-slide-to="1"
                        aria-label="Banner Ad 2"></button>
                </div>

                <!-- Carousel Slides -->
                <div class="carousel-inner">
                    <!-- Banner Ad Slide 1 -->
                    <div class="carousel-item active" data-bs-interval="4500">
                        <picture>
                            <!-- Mobile Banner (< 768px) -->
                            <source media="(max-width: 767.98px)"
                                srcset="https://d324x08swjodnj.cloudfront.net/assets/images/marketting-banners/October2026/HPXIaAyrTpWKhkNEIAua.png">
                            <!-- Desktop / Web Banner (>= 768px) -->
                            <img src="https://d324x08swjodnj.cloudfront.net/assets/images/marketting-banners/October2026/KSd3k3rQlfnqHJ8qN4V1.png"
                                alt="Durgo Utsav Promotional Banner 1"
                                class="img-fluid w-100 d-block marketing-banner-img" fetchpriority="high">
                        </picture>
                    </div>

                    <!-- Banner Ad Slide 2 (Ready for incoming ad banners) -->
                    <div class="carousel-item" data-bs-interval="4500">
                        <picture>
                            <!-- Mobile Banner (< 768px) -->
                            <source media="(max-width: 767.98px)" srcset="{{ asset('event-assets/images/ad-mob.png') }}">
                            <!-- Desktop / Web Banner (>= 768px) -->
                            <img src="{{ asset('event-assets/images/ad-web.png') }}" alt="Durgo Utsav Promotional Banner 2"
                                class="img-fluid w-100 d-block marketing-banner-img" loading="lazy">
                        </picture>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
