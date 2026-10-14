@php
    $confirmedTitle = $selectedParticipant ? ($selectedParticipant->display_name ?: $selectedParticipant->name) : ($votedPandal ?? null);
    $successMetaTitle = $confirmedTitle
        ? "Vote Confirmed for {$confirmedTitle} — People's Favorite Award | " . ($event->display_name ?: $event->name)
        : ($event->meta_title ?: ("Vote Confirmed — People's Favorite Award | " . ($event->display_name ?: $event->name)));
    $canonicalUrl = $selectedParticipant && $selectedParticipant->slug
        ? route('vote-success.participant', ['participant' => $selectedParticipant->slug])
        : route('all-pujas');
@endphp
<x-layouts.microsite
    :event="$event"
    :meta-title="$successMetaTitle"
    :meta-description="$event->meta_description ?: ('Thank you for voting in ' . ($event->display_name ?: $event->name) . '!')"
    :og-image="$selectedParticipant?->primary_image_url ?: ($event->og_image ?? null)"
    :canonical-url="$canonicalUrl"
    :use-success-script="true"
>
    <!-- Top Navigation Header -->
    @include('frontend.partials.header')

    <main>
        <!-- Dynamic Data Bridge for success.js -->
        <script>
            window.dynamicPujasData = @json($pujasData ?? []);
            window.serverVotedPandal = @json($votedPandal ?? ($selectedParticipant ? ($selectedParticipant->display_name ?: $selectedParticipant->name) : null));
            window.serverVotedCategory = @json($votedCategory ?? null);
            window.serverVotedMobile = @json($votedMobile ?? null);
            window.serverParticipantSlug = @json($selectedParticipant?->slug ?? null);
            window.serverEventName = @json($event->display_name ?: $event->name);
        </script>

        <!-- Compact Banner below Header -->
        <section class="vote-small-banner text-center" id="voteSuccessBannerSection">
            <div class="container py-2">
                <h1 class="vote-banner-title mb-1">Vote Confirmed</h1>
                <p class="vote-banner-subtitle mb-0">{{ $event->display_name ?: $event->name }} • Verified Public Ballot</p>
            </div>
        </section>

        <!-- Main Success & Voucher Section -->
        <section class="success-page-section" id="successSection">
            <div class="container" style="max-width: 860px;">

                <div class="success-main-card text-center">

                    <!-- Animated Glowing Checkmark Icon -->
                    <div class="success-hero-checkmark">
                        <i class="bi bi-check2"></i>
                    </div>

                    <!-- Main Confirmation Heading -->
                    <span class="badge bg-success text-white rounded-pill px-3 py-1 mb-2 fw-semibold">
                        <i class="bi bi-shield-check me-1"></i> VOTE OFFICIALLY RECORDED
                    </span>
                    <h2 class="success-title">Thank You! Your Vote Has Been Counted</h2>
                    <p class="text-muted small mb-4">
                        Your valuable feedback has been securely added to the official {{ $event->display_name ?: $event->name }} public rankings.
                    </p>

                    <!-- Confirmed Vote Summary Box -->
                    <div class="vote-confirmed-summary-box">
                        <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                            <span class="badge bg-danger text-white rounded-pill px-3 py-1">
                                <i class="bi bi-award-fill me-1"></i> Your Chosen Pandal
                            </span>
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold" id="confirmedCategoryBadge" style="display: none;"></span>
                        </div>
                        <h3 class="fw-bold text-dark mb-1 fs-5" id="confirmedPandalName"></h3>
                        <div class="small text-muted d-flex flex-wrap align-items-center justify-content-center gap-2">
                            <span id="confirmedVoterNameWrap" style="display: none;">
                                <i class="bi bi-person-fill text-danger me-1"></i>
                                <span>Voter: <strong class="text-dark" id="confirmedVoterName"></strong></span>
                            </span>
                            <span id="confirmedPandalLocWrap" style="display: none;">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                <span id="confirmedPandalLoc"></span>
                            </span>
                            <span id="confirmedVoterMobileWrap" style="display: none;">
                                <i class="bi bi-phone-fill text-success me-1"></i>
                                <span>Verified Mobile: <strong class="text-dark" id="confirmedVoterMobile"></strong></span>
                            </span>
                        </div>
                    </div>

                    <!-- TONI&GUY Exclusive Festive Voucher Offer -->
                    <div class="toniguy-coupon-card text-center">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <div class="coupon-brand-wrap">
                                <img src="{{ asset('event-assets/images/logo-tg.jpg') }}" alt="TONI&GUY Hairdressing" class="img-fluid">
                            </div>
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold">
                                <i class="bi bi-trophy-fill me-1"></i> OFFICIAL TROPHY SPONSOR
                            </span>
                        </div>

                        <div class="mb-2">
                            <span class="text-uppercase fw-bold text-warning small letter-spacing-1">
                                <i class="bi bi-gift-fill me-1"></i> Special Festive Reward For Voters
                            </span>
                            <h3 class="coupon-discount-text">FLAT 20% OFF</h3>
                            <p class="text-white-50 small mb-2" style="max-width: 580px; margin: 0 auto;">
                                Thank you for taking part in community voting! Enjoy an exclusive <strong>20% discount</strong> on all salon styling, hair treatments &amp; festive makeover services at TONI&amp;GUY Dibrugarh.
                            </p>
                        </div>

                        <!-- Coupon Code Display & Copy Button -->
                        <div class="coupon-code-pill-box">
                            <div>
                                <span class="text-white-50 small d-block mb-1 text-uppercase fw-semibold"
                                    style="letter-spacing: 1px;">Coupon Code:</span>
                                <span class="coupon-code-text" id="couponCodeDisplay">TONIGUY2026</span>
                            </div>
                            <button type="button" class="btn-copy-coupon" id="btnCopyCoupon"
                                title="Click to copy coupon code">
                                <i class="bi bi-clipboard me-1" id="copyIcon"></i>
                                <span id="copyBtnText">Copy Code</span>
                            </button>
                        </div>

                        <!-- Copy feedback toast alert -->
                        <div id="copySuccessAlert"
                            class="alert alert-success py-1 px-3 d-inline-block small d-none mb-2 rounded-pill">
                            <i class="bi bi-check-circle-fill me-1"></i> Coupon code copied to clipboard!
                        </div>

                        <div class="small text-white-50 mt-1">
                            <i class="bi bi-geo-alt-fill text-warning me-1"></i>
                            Valid at <strong>TONI&amp;GUY Hairdressing Salon, Dibrugarh</strong> till <strong>31st October 2026</strong>.
                            <br class="d-none d-sm-block">
                            <span class="text-white-50">Show this coupon screen or code at the reception counter during billing.</span>
                        </div>
                    </div>

                    <!-- Next Actions & Social Share -->
                    <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center gap-2 pt-2">
                        <!-- WhatsApp Share -->
                        @php
                            $sharePandalName = $confirmedTitle ?: 'my favorite Durga Puja';
                            $shareEventName = $event->display_name ?: $event->name;
                            $shareAllPujasUrl = route('all-pujas');
                            $defaultShareMessage = "🎉 I just voted for *{$sharePandalName}* in {$shareEventName}! 🏆\n\nCast your vote for your favorite puja and claim an exclusive *FLAT 20% OFF* Salon Voucher from *TONI&GUY Dibrugarh*!\n\n👉 Vote for all pujas here:\n{$shareAllPujasUrl}";
                            $defaultWhatsappLink = 'https://wa.me/?text=' . rawurlencode($defaultShareMessage);
                        @endphp
                        <a href="{{ $defaultWhatsappLink }}" id="btnShareWhatsApp"
                            data-all-pujas-url="{{ $shareAllPujasUrl }}"
                            class="btn btn-share-whatsapp w-100 w-sm-auto text-decoration-none" target="_blank"
                            rel="noopener">
                            <i class="bi bi-whatsapp fs-5"></i>
                            <span>Share on WhatsApp</span>
                        </a>

                        <!-- Explore All Pujas -->
                        <a href="{{ route('all-pujas') }}"
                            class="btn btn-outline-danger rounded-pill px-3 py-3 fw-semibold w-100 w-sm-auto">
                            <i class="bi bi-compass me-1"></i> Explore All Pujas
                        </a>

                        <!-- Return to Home -->
                        <a href="{{ route('home') }}"
                            class="btn btn-outline-secondary rounded-pill px-3 py-3 w-100 w-sm-auto">
                            <i class="bi bi-house me-1"></i> Home
                        </a>
                    </div>

                </div>

            </div>
        </section>

        <!-- Sponsors Section Wrapper (Below Voting Form) -->
        @if ($event->sponsors && $event->sponsors->where('is_active', true)->isNotEmpty())
            @include('frontend.partials.sponsors', ['compact' => true])
        @endif

        <!-- Marketing Promotional Banner Section -->
        @include('frontend.partials.marketing-banner')
    </main>

    <!-- Footer -->
    @include('frontend.partials.footer')

</x-layouts.microsite>
