@if ($event->sponsors && $event->sponsors->where('is_active', true)->isNotEmpty())
<section class="sponsors-section-wrapper" id="sponsors">
    <div class="container sponsors-container">
        <!-- ==============================================
                     TOP ROW: ORGANISED BY, TITLE, HOSPITALITY, TROPHY
                     ============================================== -->
        <div class="row g-2 g-md-3 justify-content-center">

            <!-- 1. ORGANISED BY: DIBRUGARH 24x7 -->
            <div class="col-6 col-lg-3 divider-col-right">
                <div class="sponsor-box" data-brand="Dibrugarh 24x7">
                    <div class="sponsor-title-wrap">
                        <span class="title-line"></span>
                        <span class="sponsor-title-text">ORGANISED BY</span>
                        <span class="title-line"></span>
                    </div>
                    <div class="sponsor-logo-canvas">
                        <div class="d-flex flex-column align-items-center">
                            <img src="{{ asset('event-assets/images/logo-dibru.png') }}" alt="Dibrugarh 24x7" srcset="">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. TITLE SPONSOR -->
            @if ($presentingPartner)
            <div class="col-6 col-lg-3 divider-col-right">
                <div class="sponsor-box" data-brand="{{ $presentingPartner->display_name ?: $presentingPartner->title }}">
                    <div class="sponsor-title-wrap">
                        <span class="title-line"></span>
                        <span class="sponsor-title-text">{{ strtoupper($presentingPartner->sponsor_tag ?: 'TITLE SPONSOR') }}</span>
                        <span class="title-line"></span>
                    </div>
                    <div class="sponsor-logo-canvas">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            @if ($presentingPartner->landing_url)
                                <a href="{{ $presentingPartner->landing_url }}" target="_blank" rel="noopener noreferrer">
                                    <img src="{{ $presentingPartner->logo_url }}" alt="{{ $presentingPartner->display_name ?: $presentingPartner->title }}" srcset="">
                                </a>
                            @else
                                <img src="{{ $presentingPartner->logo_url }}" alt="{{ $presentingPartner->display_name ?: $presentingPartner->title }}" srcset="">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- 3 & 4. OTHER PARTNERS / HOSPITALITY & TROPHY -->
            @php
                $topOtherSponsors = isset($otherSponsors) ? $otherSponsors->take(2) : collect();
            @endphp
            @if ($topOtherSponsors->isNotEmpty())
                @foreach ($topOtherSponsors as $other)
                    <div class="col-6 col-lg-3 {{ $loop->first ? 'divider-col-right' : '' }}">
                        <div class="sponsor-box" data-brand="{{ $other->display_name ?: $other->title }}">
                            <div class="sponsor-title-wrap">
                                <span class="title-line"></span>
                                <span class="sponsor-title-text">{{ strtoupper($other->sponsor_tag ?: ($other->sponsor_type?->label() ?: 'OFFICIAL PARTNER')) }}</span>
                                <span class="title-line"></span>
                            </div>
                            <div class="sponsor-logo-canvas">
                                <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                    @if ($other->landing_url)
                                        <a href="{{ $other->landing_url }}" target="_blank" rel="noopener noreferrer">
                                            <img src="{{ $other->logo_url }}" alt="{{ $other->display_name ?: $other->title }}" srcset="">
                                        </a>
                                    @else
                                        <img src="{{ $other->logo_url }}" alt="{{ $other->display_name ?: $other->title }}" srcset="">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

        </div>

        <!-- ==============================================
                     MIDDLE DIVIDER: ASSOCIATE PARTNERS
                     ============================================== -->
        @if (isset($associateSponsors) && $associateSponsors->isNotEmpty())
        <div class="associate-divider-wrap">
            <div class="long-divider-line"></div>
            <div class="associate-title-pill">
                <i class="bi bi-award-fill me-1" style="color: #ffcc00;"></i> ASSOCIATE PARTNERS
            </div>
            <div class="long-divider-line"></div>
        </div>

        <!-- ==============================================
                     BOTTOM ROW: ASSOCIATE PARTNERS
                     ============================================== -->
        <div class="row g-2 g-md-3 justify-content-center align-items-stretch">
            @foreach ($associateSponsors as $partner)
            <div class="col-12 col-md-3 {{ !$loop->last ? 'partner-divider-right' : '' }}">
                <div class="partner-box" data-brand="{{ $partner->display_name ?: $partner->title }}">
                    @if ($partner->landing_url)
                        <a href="{{ $partner->landing_url }}" target="_blank" rel="noopener noreferrer" class="d-flex flex-column align-items-center text-decoration-none">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $partner->logo_url }}" alt="{{ $partner->display_name ?: $partner->title }}" srcset="">
                            </div>
                            @if ($partner->sponsor_tag)
                                <div style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 0.55rem; color: #0077b6; letter-spacing: 0.4px; margin-top: 1px;">
                                    {{ $partner->sponsor_tag }}
                                </div>
                            @endif
                        </a>
                    @else
                        <div class="d-flex flex-column align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $partner->logo_url }}" alt="{{ $partner->display_name ?: $partner->title }}" srcset="">
                            </div>
                            @if ($partner->sponsor_tag)
                                <div style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 0.55rem; color: #0077b6; letter-spacing: 0.4px; margin-top: 1px;">
                                    {{ $partner->sponsor_tag }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
@endif