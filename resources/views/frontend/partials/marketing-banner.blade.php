@php
    $marketingBanners = $banners ?? ($event?->banners ?? null);

    if (! $marketingBanners && ! isset($event)) {
        $activeEvent = \App\Models\Event::where('status', 'active')->latest('year')->first();
        $marketingBanners = $activeEvent?->banners()
            ->where('category', \App\Enums\BannerCategory::Marketing)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    if ($marketingBanners instanceof \Illuminate\Database\Eloquent\Collection || $marketingBanners instanceof \Illuminate\Support\Collection) {
        $marketingBanners = $marketingBanners
            ->filter(fn ($b) => ($b->category === \App\Enums\BannerCategory::Marketing || $b->category === 'marketing') && $b->is_active)
            ->sortBy('sort_order')
            ->values();
    } else {
        $marketingBanners = collect($marketingBanners ?: []);
    }
@endphp

@if($marketingBanners->isNotEmpty())
    <!-- Marketing Promotional Banner Section (Auto-Rotating Carousel) -->
    <section class="marketing-banner-section py-4" id="promo-banner">
        <div class="container">
            <div class="marketing-banner-wrapper rounded-3 overflow-hidden shadow-sm position-relative">
                <div id="marketingBannerCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel"
                    data-bs-interval="4500" data-bs-pause="hover" data-bs-touch="true">

                    <!-- Carousel Indicators (Only rendered when multiple slides exist) -->
                    @if($marketingBanners->count() > 1)
                        <div class="carousel-indicators marketing-carousel-indicators mb-2">
                            @foreach($marketingBanners as $index => $banner)
                                <button type="button"
                                    data-bs-target="#marketingBannerCarousel"
                                    data-bs-slide-to="{{ $index }}"
                                    class="{{ $loop->first ? 'active' : '' }}"
                                    aria-current="{{ $loop->first ? 'true' : 'false' }}"
                                    aria-label="Banner Ad {{ $index + 1 }}"></button>
                            @endforeach
                        </div>
                    @endif

                    <!-- Carousel Slides -->
                    <div class="carousel-inner">
                        @foreach($marketingBanners as $banner)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}" data-bs-interval="4500">
                                @if(!empty($banner->cta_link))
                                    <a href="{{ $banner->cta_link }}"
                                        @if(str_starts_with($banner->cta_link, 'http://') || str_starts_with($banner->cta_link, 'https://'))
                                            target="_blank" rel="noopener noreferrer"
                                        @endif
                                        class="d-block text-decoration-none">
                                @endif

                                <picture>
                                    <!-- Mobile Banner (< 768px) -->
                                    @if($banner->mobile_image_url)
                                        <source media="(max-width: 767.98px)" srcset="{{ $banner->mobile_image_url }}">
                                    @endif
                                    <!-- Desktop / Web Banner (>= 768px) -->
                                    <img src="{{ $banner->desktop_image_url ?: $banner->mobile_image_url }}"
                                        alt="{{ $banner->title ?: 'Marketing Banner' }}"
                                        class="img-fluid w-100 d-block marketing-banner-img"
                                        {{ $loop->first ? 'fetchpriority=high' : 'loading=lazy' }}>
                                </picture>

                                @if(!empty($banner->cta_link))
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Carousel Controls (Previous / Next arrows when multiple slides exist) -->
                    @if($marketingBanners->count() > 1)
                        <button class="carousel-control-prev marketing-carousel-control" type="button"
                            data-bs-target="#marketingBannerCarousel" data-bs-slide="prev" aria-label="Previous Banner">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next marketing-carousel-control" type="button"
                            data-bs-target="#marketingBannerCarousel" data-bs-slide="next" aria-label="Next Banner">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    @endif

                </div>
            </div>
        </div>
    </section>
@endif
