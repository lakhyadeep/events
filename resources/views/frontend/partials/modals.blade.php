<div class="modal fade" id="shortsVideoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered shorts-modal-dialog">
            <div class="modal-content shorts-modal-content">

                <!-- 9:16 Shorts Container -->
                <div class="shorts-player-container" id="shortsPlayerContainer">

                    <!-- HTML5 Video Element with splash poster -->
                    <video id="shortsVideoElement" class="shorts-video-element" loop playsinline muted>
                    </video>

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
                            <span id="shortsLocationLabel">Dibrugarh 24Ã7 Shorts</span>
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
                            <span id="shortsLikeCount">14.2K</span>
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
                        <span class="badge bg-danger rounded-pill">Official Ballot</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body pt-3">
                    <h4 class="fw-bold mb-1" id="voteModalLabel">Cast Your Vote</h4>
                    <p class="text-muted small mb-4">Choose your favorite Pandal committee for the 2026 People's Choice
                        Award.</p>

                    <form id="voteForm">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Select Committee</label>
                            <select class="form-select" id="pandalSelect" required>
                                <option value="Thana Chariali Sarbajanin" selected>Thana Chariali Sarbajanin</option>
                                <option value="Graham Bazar Sarbajanin">Graham Bazar Sarbajanin</option>
                                <option value="New Market Durga Puja">New Market Durga Puja</option>
                                <option value="Marwari Patty Sarbajanin">Marwari Patty Sarbajanin</option>
                                <option value="Railway Colony Puja Samiti">Railway Colony Puja Samiti</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Your Mobile Number (For Verification OTP)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">+91</span>
                                <input type="tel" class="form-control" placeholder="Enter 10-digit number"
                                    maxlength="10" required pattern="[0-9]{10}">
                            </div>
                            <div class="form-text">One vote per phone number to ensure fair community polling.</div>
                        </div>

                        <div id="voteAlert" class="alert alert-success d-none py-2 px-3 small">
                            <i class="bi bi-check-circle-fill me-1"></i> Vote recorded successfully! Thank you for
                            voting.
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-vote-pill justify-content-center py-2">
                                <span>Submit My Vote</span>
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
