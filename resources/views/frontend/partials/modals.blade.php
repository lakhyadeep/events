<div class="modal fade" id="shortsVideoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered shorts-modal-dialog">
            <div class="modal-content shorts-modal-content">

                <!-- 9:16 Shorts Container -->
                <div class="shorts-player-container" id="shortsPlayerContainer">

                    <!-- HTML5 Video Element with splash poster -->
                    <video id="shortsVideoElement" class="shorts-video-element d-none" loop playsinline muted>
                    </video>

                    <!-- YouTube / Embedded Video Iframe -->
                    <iframe id="shortsIframeElement" class="shorts-iframe-element d-none"
                        src=""
                        title="Shorts Video Player"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin"
                        allowfullscreen>
                    </iframe>

                    <!-- Vignette & Shadows -->
                    <div class="shorts-vignette"></div>

                    <!-- Temporary Toast Alert -->
                    <div class="share-toast" id="shareToast"><i class="bi bi-check-circle-fill text-success me-1"></i>
                        Reel link copied!</div>

                    <!-- Center Tap Indicator -->
                    <div class="center-play-indicator" id="centerPlayIndicator">
                        <i class="bi bi-play-fill" id="indicatorIcon"></i>
                    </div>

                    <!-- Top Header -->
                    <div class="shorts-header">
                        <div class="shorts-badge">
                            <i class="bi bi-broadcast text-danger"></i>
                            <span id="shortsLocationLabel">Dibrugarh 24&times;7 Shorts</span>
                        </div>
                        <button type="button" class="shorts-close-btn" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <!-- Right Vertical Actions (Reels / Shorts Style) -->
                    <div class="shorts-actions">
                        <!-- Like Action -->
                        <button class="action-pill-btn" id="shortsLikeBtn" title="Like this Short">
                            <div class="action-circle-icon text-danger">
                                <i class="bi bi-heart-fill"></i>
                            </div>
                            <span id="shortsLikeCount" class="d-none"></span>
                        </button>

                        <!-- Mute / Unmute Action -->
                        <button class="action-pill-btn" id="shortsAudioBtn" title="Toggle Sound">
                            <div class="action-circle-icon text-warning">
                                <i class="bi bi-volume-mute-fill" id="audioIcon"></i>
                            </div>
                            <span id="audioLabel">Muted</span>
                        </button>

                        <!-- Share Action -->
                        <button class="action-pill-btn" id="shortsShareBtn" title="Share Reel">
                            <div class="action-circle-icon">
                                <i class="bi bi-share-fill"></i>
                            </div>
                            <span>Share</span>
                        </button>
                    </div>

                    <!-- Bottom Information & Progress -->
                    <div class="shorts-footer">
                        <!-- Scrubbable Progress Bar -->
                        <div class="shorts-progress-bar" id="shortsProgressBar">
                            <div class="shorts-progress-fill" id="shortsProgressFill"></div>
                        </div>

                        <!-- Title & Audio Info -->
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h3 class="shorts-pandal-title" id="shortsModalPandalName">Santipara 2nd Lane Puja
                            </h3>
                            <div class="sound-wave" id="soundWaveBar">
                                <span></span><span></span><span></span><span></span>
                            </div>
                        </div>

                        <p class="shorts-theme-desc" id="shortsModalTheme">Theme: Swarna Mandir Golden Splendor
                        </p>

                        <!-- Navigation Controls (Previous / Next Short) -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="shorts-nav-arrows">
                                <button class="shorts-nav-btn" id="prevShortBtn">
                                    <i class="bi bi-chevron-up"></i> Prev
                                </button>
                                <button class="shorts-nav-btn" id="nextShortBtn">
                                    Next <i class="bi bi-chevron-down"></i>
                                </button>
                            </div>
                            <span class="text-white-50" style="font-size: 0.74rem;">Tap video to pause</span>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
    <!-- Vote Interactive Modal -->
    <div class="modal fade" id="voteModal" tabindex="-1" aria-labelledby="voteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ asset('event-assets/images/logo-dibru.png') }}" alt="Dibrugarh 24x7 Logo" class="img-fluid"
                            style="max-height: 36px;">
                        <span class="badge bg-danger rounded-pill">Official Vote</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body pt-3">
                    <h4 class="fw-bold mb-1" id="voteModalLabel">Cast Your Vote</h4>
                    <p class="text-muted small mb-4">Choose your favorite Pandal committee for the {{ $event->year ?? 2026 }} People's Choice Award.</p>

                    @php
                        $isVotingOpen = isset($event) && $event->isVotingOpen();
                    @endphp

                    @if (! $isVotingOpen)
                        <div class="alert alert-warning py-3 px-3 d-flex align-items-center gap-2 mb-3 rounded-3" id="votingClosedModalAlert">
                            <i class="bi bi-lock-fill fs-4 text-warning"></i>
                            <div>
                                <div class="fw-bold text-dark">Voting is Closed</div>
                                <div class="small text-muted">{{ isset($event) ? $event->getVotingClosedMessage() : 'Voting is currently closed for this event.' }}</div>
                            </div>
                        </div>
                    @endif

                    <form id="voteForm">
                        <input type="hidden" id="voteEventId" value="{{ $event->id ?? '' }}">

                        <input type="hidden" id="awardSelect" value="">

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Select Committee</label>
                            <select class="form-select" id="pandalSelect" required {{ ! $isVotingOpen ? 'disabled' : '' }}>
                                @if (isset($participants) && $participants->isNotEmpty())
                                    @foreach ($participants as $pandal)
                                        <option value="{{ $pandal->id }}" data-name="{{ $pandal->name }}" {{ $loop->first ? 'selected' : '' }}>
                                            {{ $pandal->display_name ?: $pandal->name }} ({{ $pandal->locality ?: ($pandal->zone ?: 'Dibrugarh') }})
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled selected>No committees available for voting</option>
                                @endif
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Your Mobile Number (For Verification OTP)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">+91</span>
                                <input type="tel" class="form-control" id="voterMobile"
                                    placeholder="Enter 10-digit number" maxlength="10" required pattern="[0-9]{10}" {{ ! $isVotingOpen ? 'disabled' : '' }}>
                            </div>
                            <div class="form-text">
                                <i class="bi bi-shield-check text-success me-1"></i>One vote per phone number for each candidate or award category.
                            </div>
                        </div>

                        <!-- Already Voted Warning Message -->
                        <div id="alreadyVotedAlert" class="alert alert-danger d-none py-2 px-3 small mt-2">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            <span id="alreadyVotedMsg">This mobile number has already cast a vote according to event voting rules.</span>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-vote-pill justify-content-center py-2" id="btnSendOtp" {{ ! $isVotingOpen ? 'disabled' : '' }}>
                                <span>{{ $isVotingOpen ? 'Proceed to OTP Verification' : 'Voting Closed' }}</span>
                                <i class="bi bi-{{ $isVotingOpen ? 'arrow-right' : 'lock-fill' }} ms-1"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @php
        $configuredOtpLength = \App\Models\OtpSetting::current()->otp_length ?? 6;
    @endphp
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
                        We have sent a <span id="displayOtpLength">{{ $configuredOtpLength }}</span>-digit OTP to <strong id="displayMobileNum" class="text-dark">+91 XXXXX
                            XXXXX</strong>
                    </p>

                    <form id="otpForm">
                        <!-- Dynamic Digit OTP Input Boxes -->
                        <div class="d-flex justify-content-center flex-wrap gap-2 my-3" id="otpInputContainer" data-otp-length="{{ $configuredOtpLength }}">
                            @for ($i = 1; $i <= $configuredOtpLength; $i++)
                                <input type="text" class="form-control text-center fs-4 fw-bold otp-digit-box" maxlength="1"
                                    pattern="[0-9]" inputmode="numeric" required autocomplete="off" data-index="{{ $i }}"
                                    style="width: 44px; height: 52px; border-radius: 10px; border: 2px solid #dee2e6;">
                            @endfor
                        </div>

                        <!-- Invalid OTP Alert Message -->
                        <div id="otpInvalidAlert" class="alert alert-danger d-none py-2 px-3 small text-start mb-3">
                            <i class="bi bi-x-circle-fill me-1"></i> <span id="otpInvalidMsg">Invalid OTP entered. Please try again.</span>
                        </div>

                        <!-- Valid OTP Alert Message -->
                        <div id="otpValidAlert" class="alert alert-success d-none py-2 px-3 small text-start mb-3">
                            <i class="bi bi-check-circle-fill me-1"></i> <span id="otpValidMsg">OTP verified
                                successfully! Recording your vote...</span>
                        </div>

                        <!-- Vote Submitted Success Alert Message -->
                        <div id="voteSubmittedAlert" class="alert alert-success d-none py-2 px-3 small text-start mb-3">
                            <i class="bi bi-patch-check-fill me-1"></i> <span id="voteSubmittedMsg">Vote submitted
                                successfully! Thank you for participating in Sharad Samman 2026.</span>
                        </div>

                        <!-- Resend OTP Alert Toast Message -->
                        <div id="otpResendAlert" class="alert alert-warning d-none py-2 px-3 small text-start mb-3">
                            <i class="bi bi-send-check-fill me-1"></i> <span id="otpResendMsg">A new verification code has been dispatched to your mobile number!</span>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-vote-pill justify-content-center py-2"
                                id="btnVerifyOtp">
                                <span>Verify OTP &amp; Cast Vote</span>
                                <i class="bi bi-check-lg ms-1"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-link text-muted text-decoration-none mt-1"
                                id="btnResendOtp">
                                Didn't receive OTP? <span id="resendBtnText" class="text-danger fw-semibold">Resend OTP</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
