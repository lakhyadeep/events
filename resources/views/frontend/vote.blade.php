@php
    $pandalTitle = $selectedParticipant ? ($selectedParticipant->display_name ?: $selectedParticipant->name) : null;
    $voteMetaTitle = $pandalTitle 
        ? "Vote for {$pandalTitle} — People's Favorite Award | " . ($event->display_name ?: $event->name)
        : ($event->meta_title ?: ("Vote Now — People's Favorite Award | " . ($event->display_name ?: $event->name)));
    $voteMetaDesc = $pandalTitle
        ? "Cast your vote for {$pandalTitle} in " . ($event->display_name ?: $event->name) . ". Vote for theme, pandal, idol, or overall best."
        : ($event->meta_description ?: ("Cast your vote for the People's Favorite Award in " . ($event->display_name ?: $event->name) . ". Select your favorite puja committee and vote for theme, pandal, idol, or overall best."));
    $isEventSlugRoute = request()->routeIs('events.*') || request()->route('slug');
    $canonicalUrl = $selectedParticipant && $selectedParticipant->slug
        ? ($isEventSlugRoute ? route('events.vote.participant', ['slug' => $event->slug, 'participant' => $selectedParticipant->slug]) : route('vote.participant', ['participant' => $selectedParticipant->slug]))
        : ($isEventSlugRoute ? route('events.vote', $event->slug) : route('vote'));
@endphp
<x-layouts.microsite
    :event="$event"
    :meta-title="$voteMetaTitle"
    :meta-description="$voteMetaDesc"
    :og-image="$selectedParticipant?->primary_image_url ?: ($event->og_image ?? null)"
    :canonical-url="$canonicalUrl"
    :use-vote-script="true"
>
    <!-- Top Navigation Header -->
    @include('frontend.partials.header')

    <main>
        <!-- Compact Banner below Header -->
        <section class="vote-small-banner text-center" id="voteBanner">
            <div class="container py-2">
                <h1 class="vote-banner-title mb-1">Vote</h1>
                @if (!empty($event->tagline))
                    <p class="vote-banner-subtitle mb-0">{{ $event->tagline }}</p>
                @endif
            </div>
        </section>

        <!-- Dynamic Data Bridge for vote.js -->
        <script>
            window.dynamicPujasData = @json($pujasData);
            window.currentEventId = {{ $event->id }};
            window.currentEventName = @json($event->display_name ?: $event->name);
            window.selectedParticipantId = {{ $selectedParticipant?->id ?? 'null' }};
            window.selectedParticipantSlug = @json($selectedParticipant?->slug ?? null);
            window.voteBaseUrl = @json($isEventSlugRoute ? route('events.vote', $event->slug) : route('vote'));
        </script>

        <!-- Voting Form Section: People's Favorite Award -->
        <section class="vote-page-section" id="voting-form-section">
            <div class="container" style="max-width: 860px;">

                <!-- Grand Vote Success Toast Banner -->
                <div id="voteSuccessBanner" class="vote-success-toast-banner text-center" role="alert">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white text-success mb-2"
                        style="width: 48px; height: 48px; font-size: 1.5rem;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <h4 class="fw-bold mb-1">Thank You! Your Vote Has Been Recorded</h4>
                    <p class="mb-0 text-white-50">
                        You voted for <strong class="text-white" id="successPandalConfirmed"></strong> in the category
                        <strong class="text-warning" id="successFeatureConfirmed"></strong>.
                    </p>
                </div>

                <!-- Form Card -->
                <div class="vote-form-card">

                    <!-- Section Header Box -->
                    <div class="text-center mb-4">
                        <span class="badge bg-danger text-white rounded-pill px-3 py-1 mb-2 fw-semibold">
                            <i class="bi bi-trophy-fill me-1 text-warning"></i> OFFICIAL PUBLIC BALLOT @if($event->year) • {{ $event->year }} @endif
                        </span>
                        <h2 class="vote-page-title">People's favorite award</h2>

                        <!-- Ornamental Mandala Divider -->
                        <div class="ornamental-divider-wrapper" aria-hidden="true">
                            <span class="divider-line"></span>
                            <span class="divider-icon">
                                <svg class="mandala-symbol" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect x="9.5" y="9.5" width="5" height="5" fill="#8c0c16"
                                        transform="rotate(45 12 12)" />
                                    <path d="M12 2C12.8 4.2 13.5 6.5 12 8.5C10.5 6.5 11.2 4.2 12 2Z" fill="#8c0c16" />
                                    <path d="M12 22C12.8 19.8 13.5 17.5 12 15.5C10.5 17.5 11.2 19.8 12 22Z"
                                        fill="#8c0c16" />
                                    <path d="M2 12C4.2 12.8 6.5 13.5 8.5 12C6.5 10.5 4.2 11.2 2 12Z" fill="#8c0c16" />
                                    <path d="M22 12C19.8 12.8 17.5 13.5 15.5 12C17.5 10.5 19.8 11.2 22 12Z"
                                        fill="#8c0c16" />
                                    <circle cx="6.5" cy="6.5" r="1.5" fill="#c68a4c" />
                                    <circle cx="17.5" cy="6.5" r="1.5" fill="#c68a4c" />
                                    <circle cx="6.5" cy="17.5" r="1.5" fill="#c68a4c" />
                                    <circle cx="17.5" cy="17.5" r="1.5" fill="#c68a4c" />
                                </svg>
                            </span>
                            <span class="divider-line right-line"></span>
                        </div>

                        @if (!empty($event->about_text))
                            <p class="text-muted small mb-0">{!!  $event->about_text !!}</p>
                        @endif
                    </div>

                    <!-- Server Flash Error Message (Above Form) -->
                    @if (session('error') || session('alert') || $errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-4 p-3 mb-4 d-flex align-items-start gap-2" role="alert">
                            <i class="bi bi-exclamation-triangle-fill text-danger fs-5 flex-shrink-0 mt-1"></i>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1 text-danger">Voting Error</h6>
                                <p class="small mb-0 text-danger-emphasis">{{ session('error') ?? session('alert') ?? $errors->first() }}</p>
                            </div>
                            <button type="button" class="btn-close btn-sm ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Client Error Alert (Above Form) -->
                    <div id="formErrorAlert" class="alert alert-danger border-0 shadow-sm rounded-4 p-3 mb-4 d-none" role="alert">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-exclamation-octagon-fill text-danger fs-5 flex-shrink-0 mt-1"></i>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1 text-danger" id="formErrorHeading">Please Check Your Input</h6>
                                <div class="small text-danger-emphasis" id="formErrorMsg"></div>
                            </div>
                            <button type="button" class="btn-close btn-sm ms-auto" aria-label="Close" onclick="$('#formErrorAlert').addClass('d-none');"></button>
                        </div>
                    </div>

                    <!-- Already Voted Alert (Above Form) -->
                    <div id="alreadyVotedAlert" class="alert alert-danger border-0 shadow-sm rounded-4 p-3 mb-4 d-none" role="alert">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-shield-exclamation text-danger fs-5 flex-shrink-0 mt-1"></i>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1 text-danger">Ballot Limit Reached</h6>
                                <div class="small text-danger-emphasis" id="alreadyVotedMsg">This mobile number has already cast a vote in {{ $event->display_name ?: $event->name }}. Only 1 vote per mobile number is permitted.</div>
                            </div>
                            <button type="button" class="btn-close btn-sm ms-auto" aria-label="Close" onclick="$('#alreadyVotedAlert').addClass('d-none');"></button>
                        </div>
                    </div>

                    @if (isset($event) && ! $event->isVotingOpen())
                        <div class="alert alert-warning border-0 shadow-sm rounded-4 p-4 text-center mb-4">
                            <i class="bi bi-lock-fill fs-3 text-warning d-block mb-2"></i>
                            <h5 class="fw-bold mb-1">Voting is Closed</h5>
                            <p class="small text-muted mb-0">{{ $event->getVotingClosedMessage() }}</p>
                        </div>
                    @else
                        <!-- Voting Form -->
                        <form id="peopleFavoriteVoteForm" novalidate>
                            <input type="hidden" id="voteEventId" name="event_id" value="{{ $event->id }}">
                            <input type="hidden" id="voteParticipantId" name="participant_id" value="{{ $selectedParticipant?->id ?? '' }}">
                            <input type="hidden" id="votePandalSelect" name="pandal" value="{{ $selectedParticipant?->display_name ?: ($selectedParticipant?->name ?? '') }}">

                            <!-- Selected Pandal Card -->
                            <div id="selectedPandalPreview" class="selected-puja-preview mb-4" @if(!$selectedParticipant) style="display: none;" @endif>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="selected-pandal-icon-badge">
                                        <i class="bi bi-building-check"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                            <h3 class="fw-bold text-dark mb-0 fs-5" id="previewPandalName">
                                                {{ $selectedParticipant?->display_name ?: ($selectedParticipant?->name ?? '') }}
                                            </h3>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="selected-choice-badge">
                                                    <i class="bi bi-patch-check-fill text-danger me-1"></i> Nominated Choice
                                                </span>
                                                <a href="{{ isset($event) && $event->slug ? route('events.all-pujas', $event->slug) : route('all-pujas') }}" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0" style="font-size: 0.75rem;">
                                                    Change
                                                </a>
                                            </div>
                                        </div>
                                        @php
                                            $locName = is_object($selectedParticipant?->locality) ? $selectedParticipant->locality->name : ($selectedParticipant?->getRawOriginal('locality') ?: ($selectedParticipant?->locality ?: ''));
                                            $zoneName = is_object($selectedParticipant?->zone) ? $selectedParticipant->zone->name : ($selectedParticipant?->getRawOriginal('zone') ?: ($selectedParticipant?->zone ?: ''));
                                            $locString = trim(($locName ? $locName . ($zoneName ? ', ' : '') : '') . $zoneName);
                                            $themeString = $selectedParticipant?->puja_theme ?: ($selectedParticipant?->short_introduction ?: '');
                                        @endphp
                                        <div class="small text-muted mb-2">
                                            <span id="previewPandalLocWrap" @if(empty($locString)) style="display: none;" @endif>
                                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                                <span id="previewPandalLoc">{{ $locString }}</span>
                                            </span>
                                            <span id="previewPandalDivider" class="mx-1" @if(empty($locString) || empty($themeString)) style="display: none;" @endif>•</span>
                                            <span id="previewPandalThemeWrap" @if(empty($themeString)) style="display: none;" @endif>
                                                <i class="bi bi-stars text-warning me-1"></i>
                                                <span>Theme: &ldquo;</span><span id="previewPandalTheme">{{ $themeString }}</span><span>&rdquo;</span>
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <span class="badge bg-danger text-white rounded-pill px-3 py-1">
                                                <i class="bi bi-award-fill me-1"></i> Selected Pandal
                                            </span>
                                            @if ($selectedParticipant && isset($selectedParticipant->votes_count))
                                                <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold" id="previewPandalVotes">
                                                    {{ number_format($selectedParticipant->votes_count) }} votes
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold" id="previewPandalVotes" style="display: none;"></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Field 2: Award Categories to Vote For (Multi-Select Supported) -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                    <label class="form-label fw-bold text-dark mb-0">
                                        <i class="bi bi-stars text-warning me-1"></i> What do you like most here? (Vote for Award Categories) <span class="text-danger">*</span>
                                    </label>
                                    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning rounded-pill px-3 py-1 small">
                                        <i class="bi bi-check2-square text-danger me-1"></i> Multi-select supported
                                    </span>
                                </div>
                                <div class="form-text small text-muted mb-3">Choose one or more categories where this puja committee excelled:</div>

                                <div class="row g-3" id="awardPreferenceGrid">
                                    @if ($event->awards && $event->awards->where('is_active', true)->isNotEmpty())
                                        @foreach ($event->awards->where('is_active', true) as $award)
                                            @php
                                                $name = strtolower($award->name . ' ' . ($award->category ?? ''));
                                                $isCrown = $loop->first || str_contains($name, 'overall') || str_contains($name, 'serar sera') || str_contains($name, 'grand championship');

                                                if ($isCrown) {
                                                    $icon = 'bi-trophy-fill';
                                                } elseif (str_contains($name, 'theme') || str_contains($name, 'concept') || str_contains($name, 'art')) {
                                                    $icon = 'bi-palette-fill';
                                                } elseif (str_contains($name, 'pandal') || str_contains($name, 'mandap') || str_contains($name, 'architecture')) {
                                                    $icon = 'bi-bank2';
                                                } elseif (str_contains($name, 'idol') || str_contains($name, 'pratima') || str_contains($name, 'clay') || str_contains($name, 'sculpture')) {
                                                    $icon = 'bi-heart-fill';
                                                } elseif (str_contains($name, 'light') || str_contains($name, 'aalok') || str_contains($name, 'illumination')) {
                                                    $icon = 'bi-lightbulb-fill';
                                                } elseif (str_contains($name, 'eco') || str_contains($name, 'green') || str_contains($name, 'sustainability')) {
                                                    $icon = 'bi-tree-fill';
                                                } else {
                                                    $icon = 'bi-stars';
                                                }
                                            @endphp
                                            <div class="col-6 col-md-4">
                                                <label class="preference-card-label h-100" for="award_check_{{ $award->id }}">
                                                    <input type="checkbox" name="award_ids[]" value="{{ $award->id }}"
                                                        id="award_check_{{ $award->id }}"
                                                        data-award-name="{{ $award->display_name ?: $award->name }}"
                                                        data-award-category="{{ $award->category ?: $award->name }}"
                                                        class="preference-checkbox-input"
                                                        {{ $loop->first ? 'checked' : '' }}>
                                                    <div class="preference-card text-center p-3 h-100 {{ $isCrown ? 'pref-card-overall' : '' }}">
                                                        <div class="pref-icon-circle mx-auto mb-2">
                                                            <i class="bi {{ $icon }}"></i>
                                                        </div>
                                                        <span class="pref-title d-block fw-bold">{{ $award->display_name ?: $award->name }}</span>
                                                        @if ($award->category || $award->description)
                                                            <small class="pref-desc text-muted d-block">{{ $award->category ?: $award->description }}</small>
                                                        @endif
                                                        <div class="pref-check-badge"><i class="bi bi-check-circle-fill"></i></div>
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="col-12 text-center py-3 text-muted">
                                            <p class="mb-0">No award categories available for voting.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Field 3: Voter Contact Details -->
                            <div class="row g-3 mb-4 pt-2 border-top">
                                <div class="col-md-6">
                                    <label for="voterNameInput" class="form-label fw-bold text-dark small">
                                        <i class="bi bi-person-fill text-danger me-1"></i> Your Name (Optional)
                                    </label>
                                    <input type="text" class="form-control form-control-lg fs-6" id="voterNameInput"
                                        placeholder="Enter your name">
                                </div>

                                <div class="col-md-6">
                                    <label for="voterMobileInput" class="form-label fw-bold text-dark small">
                                        <i class="bi bi-phone-fill text-danger me-1"></i> Your Mobile Number (OTP Verification) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light fw-bold">+91</span>
                                        <input type="tel" class="form-control form-control-lg fs-6" id="voterMobileInput"
                                            placeholder="Enter 10-digit mobile number" maxlength="10" required
                                            pattern="[0-9]{10}">
                                    </div>
                                    <div class="form-text small text-muted">
                                        <i class="bi bi-shield-check text-success me-1"></i> One verified vote per mobile number ensures genuine public polling.
                                    </div>
                                </div>
                            </div>

                            <!-- Button: Submit your vote -->
                            <div class="text-center pt-3">
                                <button type="submit" class="btn btn-vote-pill btn-lg px-5 py-3 shadow" id="btnSubmitYourVote">
                                    <span>Submit your vote</span>
                                    <span class="vote-icon-badge">
                                        <i class="bi bi-check2-circle"></i>
                                    </span>
                                </button>
                                <div class="text-muted small mt-2">
                                    <i class="bi bi-lock-fill text-secondary me-1"></i> Verified with instant 4-digit SMS OTP
                                </div>
                            </div>
                        </form>
                    @endif

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

    <!-- OTP Verification Modal -->
    <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ asset('event-assets/images/logo-dibru.png') }}" alt="Dibrugarh 24x7 Logo" class="img-fluid"
                            style="max-height: 34px;">
                        <span class="badge bg-warning text-dark rounded-pill fw-bold">
                            <i class="bi bi-shield-lock-fill me-1"></i> Verification
                        </span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="otpCloseBtn"></button>
                </div>

                <div class="modal-body pt-3 text-center px-4">
                    <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-circle bg-danger-subtle text-danger"
                        style="width: 56px; height: 56px; font-size: 1.5rem;">
                        <i class="bi bi-phone-vibrate-fill"></i>
                    </div>
                    <h4 class="fw-bold mb-1" id="otpModalLabel">Enter Verification Code</h4>
                    <p class="text-muted small mb-3">
                        We have sent a 4-digit OTP to <strong id="displayMobileNum" class="text-dark">+91 XXXXX XXXXX</strong>
                    </p>

                    <form id="otpForm">
                        <!-- 4 Digit OTP Input Boxes -->
                        <div class="d-flex justify-content-center gap-2 my-3" id="otpInputContainer">
                            <input type="text" class="form-control text-center fs-4 fw-bold otp-digit-box" maxlength="1"
                                pattern="[0-9]" inputmode="numeric" required autocomplete="off" data-index="1">
                            <input type="text" class="form-control text-center fs-4 fw-bold otp-digit-box" maxlength="1"
                                pattern="[0-9]" inputmode="numeric" required autocomplete="off" data-index="2">
                            <input type="text" class="form-control text-center fs-4 fw-bold otp-digit-box" maxlength="1"
                                pattern="[0-9]" inputmode="numeric" required autocomplete="off" data-index="3">
                            <input type="text" class="form-control text-center fs-4 fw-bold otp-digit-box" maxlength="1"
                                pattern="[0-9]" inputmode="numeric" required autocomplete="off" data-index="4">
                        </div>

                        <!-- Invalid OTP Alert Message -->
                        <div id="otpInvalidAlert" class="alert alert-danger d-none py-2 px-3 small text-start mb-3">
                            <i class="bi bi-x-circle-fill me-1"></i> <span id="otpInvalidMsg">Invalid OTP entered! Please enter the correct 4-digit code.</span>
                        </div>

                        <!-- Valid OTP Alert Message -->
                        <div id="otpValidAlert" class="alert alert-success d-none py-2 px-3 small text-start mb-3">
                            <i class="bi bi-check-circle-fill me-1"></i> <span id="otpValidMsg">OTP verified successfully! Recording your ballot...</span>
                        </div>

                        <!-- Vote Submitted Success Alert Message -->
                        <div id="voteSubmittedAlert" class="alert alert-success d-none py-2 px-3 small text-start mb-3">
                            <i class="bi bi-patch-check-fill me-1"></i> <span id="voteSubmittedMsg">Vote submitted successfully! Thank you for participating.</span>
                        </div>

                        <!-- Resend OTP Alert Toast Message -->
                        <div id="otpResendAlert" class="alert alert-warning d-none py-2 px-3 small text-start mb-3">
                            <i class="bi bi-send-check-fill me-1"></i> A new 4-digit OTP has been sent to your mobile number!
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-vote-pill justify-content-center py-2"
                                id="btnVerifyOtp">
                                <span>Verify OTP &amp; Submit Vote</span>
                                <i class="bi bi-check-lg ms-1"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-link text-muted text-decoration-none mt-1"
                                id="btnResendOtp">
                                Didn't receive OTP? <span class="text-danger fw-semibold">Resend OTP</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-layouts.microsite>
