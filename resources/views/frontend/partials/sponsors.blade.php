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
                            <img src="{{ asset('event-assets/images/logo-dibru.png') }}" alt="Dibrugarh 24x7" class="img-fluid" style="max-height: 80px; object-fit: contain;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. TITLE SPONSOR: DYNAMIC WITH FALLBACK -->
            <div class="col-6 col-lg-3 divider-col-right">
                <div class="sponsor-box" data-brand="{{ $presentingPartner?->display_name ?? 'JSB Infrastructures' }}">
                    <div class="sponsor-title-wrap">
                        <span class="title-line"></span>
                        <span class="sponsor-title-text">{{ strtoupper($presentingPartner?->sponsor_tag ?: 'TITLE SPONSOR') }}</span>
                        <span class="title-line"></span>
                    </div>
                    <div class="sponsor-logo-canvas">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            @if ($presentingPartner && $presentingPartner->logo_url)
                                @if ($presentingPartner->landing_url)
                                    <a href="{{ $presentingPartner->landing_url }}" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ $presentingPartner->logo_url }}" alt="{{ $presentingPartner->display_name ?: $presentingPartner->title }}" class="img-fluid" style="max-height: 80px; object-fit: contain;">
                                    </a>
                                @else
                                    <img src="{{ $presentingPartner->logo_url }}" alt="{{ $presentingPartner->display_name ?: $presentingPartner->title }}" class="img-fluid" style="max-height: 80px; object-fit: contain;">
                                @endif
                            @else
                                <img src="{{ asset('event-assets/images/logo-jsb.jpg') }}" alt="JSB Infrastructures" class="img-fluid" style="max-height: 80px; object-fit: contain;">
                            @endif
                        </div>
                    </div>
                </div>
            </div>

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
                                <span class="sponsor-title-text">{{ strtoupper($other->sponsor_tag ?: $other->sponsor_type->label()) }}</span>
                                <span class="title-line"></span>
                            </div>
                            <div class="sponsor-logo-canvas">
                                <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                    @if ($other->landing_url)
                                        <a href="{{ $other->landing_url }}" target="_blank" rel="noopener noreferrer">
                                            <img src="{{ $other->logo_url }}" alt="{{ $other->display_name ?: $other->title }}" class="img-fluid" style="max-height: 80px; object-fit: contain;">
                                        </a>
                                    @else
                                        <img src="{{ $other->logo_url }}" alt="{{ $other->display_name ?: $other->title }}" class="img-fluid" style="max-height: 80px; object-fit: contain;">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <!-- Fallback: Hospitality Sponsor -->
                <div class="col-6 col-lg-3 divider-col-right">
                    <div class="sponsor-box" data-brand="Badoco Tasty Treat">
                        <div class="sponsor-title-wrap">
                            <span class="title-line"></span>
                            <span class="sponsor-title-text">HOSPITALITY SPONSOR</span>
                            <span class="title-line"></span>
                        </div>
                        <div class="sponsor-logo-canvas">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <img src="{{ asset('event-assets/images/logo-tasty.jpg') }}" alt="Badoco Tasty Treat" class="img-fluid" style="max-height: 80px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fallback: Trophy Sponsor -->
                <div class="col-6 col-lg-3">
                    <div class="sponsor-box" data-brand="TONI&GUY Hairdressing">
                        <div class="sponsor-title-wrap">
                            <span class="title-line"></span>
                            <span class="sponsor-title-text">TROPHY SPONSOR</span>
                            <span class="title-line"></span>
                        </div>
                        <div class="sponsor-logo-canvas">
                            <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                <img src="{{ asset('event-assets/images/logo-tg.jpg') }}" alt="TONI&GUY Hairdressing" class="img-fluid" style="max-height: 80px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <!-- ==============================================
             MIDDLE DIVIDER: ASSOCIATE PARTNERS
             ============================================== -->
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
            @if (isset($associateSponsors) && $associateSponsors->isNotEmpty())
                @foreach ($associateSponsors as $partner)
                    <div class="col-6 col-md-4 col-lg partner-divider-right">
                        <div class="partner-box" data-brand="{{ $partner->display_name ?: $partner->title }}">
                            @if ($partner->landing_url)
                                <a href="{{ $partner->landing_url }}" target="_blank" rel="noopener noreferrer" class="d-flex flex-column align-items-center text-decoration-none text-center">
                                    <img src="{{ $partner->logo_url }}" alt="{{ $partner->display_name ?: $partner->title }}" class="img-fluid" style="max-height: 60px; max-width: 100%; object-fit: contain;">
                                    @if ($partner->display_name || $partner->sponsor_tag)
                                        <div style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 0.65rem; color: #0077b6; letter-spacing: 0.4px; margin-top: 4px;">
                                            {{ $partner->display_name ?: $partner->title }}
                                        </div>
                                    @endif
                                </a>
                            @else
                                <div class="d-flex flex-column align-items-center text-center">
                                    <img src="{{ $partner->logo_url }}" alt="{{ $partner->display_name ?: $partner->title }}" class="img-fluid" style="max-height: 60px; max-width: 100%; object-fit: contain;">
                                    @if ($partner->display_name || $partner->sponsor_tag)
                                        <div style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 0.65rem; color: #0077b6; letter-spacing: 0.4px; margin-top: 4px;">
                                            {{ $partner->display_name ?: $partner->title }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <!-- Fallback 1: DIGITAL PILOT -->
                <div class="col-12 col-md-3 partner-divider-right">
                    <div class="partner-box" data-brand="Digital Pilot">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('event-assets/images/logo-dp.png') }}" alt="Digital Pilot" class="img-fluid">
                        </div>
                    </div>
                </div>

                <!-- Fallback 2: CONCEPTCORE LEARNING -->
                <div class="col-12 col-md-3 partner-divider-right">
                    <div class="partner-box" data-brand="ConceptCore Learning">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('event-assets/images/logo-cc.png') }}" alt="ConceptCore Learning" class="img-fluid">
                        </div>
                    </div>
                </div>

                <!-- Fallback 3: KINLEY -->
                <div class="col-12 col-md-3">
                    <div class="partner-box" data-brand="Kinley - With Added Minerals">
                        <div class="d-flex flex-column align-items-center">
                            <div class="d-flex align-items-baseline position-relative">
                                <img src="{{ asset('event-assets/images/logo-kinley.png') }}" alt="Kinley" class="img-fluid">
                            </div>
                            <div style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 0.55rem; color: #0077b6; letter-spacing: 0.4px; margin-top: 1px;">
                                With Added Minerals
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
