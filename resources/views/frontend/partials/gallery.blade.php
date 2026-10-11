<section class="glimpses-section" id="gallery">

    <!-- Decorative Traditional Alpana Corner Motifs -->
    <div class="alpana-corner alpana-top-left" aria-hidden="true">
        <svg viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 0 C 50 15, 90 48, 108 98 L96 108 C 80 62, 45 28, 0 12 Z" fill="#b07b3b" fill-opacity="0.38" />
            <circle cx="56" cy="56" r="9" stroke="#800c19" stroke-width="1.8" stroke-dasharray="3 3" />
            <path d="M14 46 C 30 40, 52 48, 58 68" stroke="#b07b3b" stroke-width="1.6" />
            <path d="M46 14 C 40 30, 48 52, 68 58" stroke="#b07b3b" stroke-width="1.6" />
            <circle cx="32" cy="32" r="5" fill="#800c19" fill-opacity="0.6" />
            <path d="M10 10 L38 38 M14 70 L70 14" stroke="#b07b3b" stroke-width="1.3" stroke-linecap="round" />
        </svg>
    </div>

    <div class="alpana-corner alpana-top-right" aria-hidden="true">
        <svg viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 0 C 50 15, 90 48, 108 98 L96 108 C 80 62, 45 28, 0 12 Z" fill="#b07b3b" fill-opacity="0.38" />
            <circle cx="56" cy="56" r="9" stroke="#800c19" stroke-width="1.8" stroke-dasharray="3 3" />
            <path d="M14 46 C 30 40, 52 48, 58 68" stroke="#b07b3b" stroke-width="1.6" />
            <path d="M46 14 C 40 30, 48 52, 68 58" stroke="#b07b3b" stroke-width="1.6" />
            <circle cx="32" cy="32" r="5" fill="#800c19" fill-opacity="0.6" />
            <path d="M10 10 L38 38 M14 70 L70 14" stroke="#b07b3b" stroke-width="1.3" stroke-linecap="round" />
        </svg>
    </div>

    <div class="alpana-corner alpana-bottom-left" aria-hidden="true">
        <svg viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 0 C 50 15, 90 48, 108 98 L96 108 C 80 62, 45 28, 0 12 Z" fill="#b07b3b" fill-opacity="0.3" />
            <circle cx="56" cy="56" r="8" stroke="#800c19" stroke-width="1.6" />
        </svg>
    </div>

    <div class="alpana-corner alpana-bottom-right" aria-hidden="true">
        <svg viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 0 C 50 15, 90 48, 108 98 L96 108 C 80 62, 45 28, 0 12 Z" fill="#b07b3b" fill-opacity="0.3" />
            <circle cx="56" cy="56" r="8" stroke="#800c19" stroke-width="1.6" />
        </svg>
    </div>

    <div class="festive-bottom-trim" aria-hidden="true"></div>

    <div class="glimpses-container">

        <!-- Section Title & Subtitle -->
        <h2 class="glimpses-title">Glimpses of Puja {{ $event->year ?? 2026 }}</h2>
        <p class="glimpses-subtitle">Moments, memories and magic from Dibrugarh's most loved pujas</p>

        <!-- Carousel Stage -->
        <div class="slider-stage">

            <!-- Left Circular Navigation Button -->
            <button class="nav-arrow-btn nav-arrow-prev" id="prevReelBtn" aria-label="Previous reels">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>

            <!-- Carousel Viewport -->
            <div class="reels-viewport" id="reelsViewport">
                <div class="reels-track" id="reelsTrack">
                    @if ($event->videoShorts && $event->videoShorts->isNotEmpty())
                        @foreach ($event->videoShorts as $index => $short)
                            <div class="reel-card" data-index="{{ $index }}" data-pandal="{{ $short->name }}"
                                data-likes="{{ (14 + $index * 5) . '.2K' }}" data-theme="{{ $short->caption ?: $short->name }}"
                                data-splash="{{ $short->thumbnail_image_url ?: asset('event-assets/images/hero.png') }}"
                                data-video="{{ $short->video_url }}">
                                <video class="reel-bg"
                                    poster="{{ $short->thumbnail_image_url ?: asset('event-assets/images/hero.png') }}"
                                    src="{{ $short->video_url }}" muted loop
                                    playsinline></video>
                                <div class="reel-overlay"></div>
                                <div class="reel-play-btn" title="Watch Short">
                                    <svg viewBox="0 0 24 24">
                                        <polygon points="6,3 20,12 6,21"></polygon>
                                    </svg>
                                </div>
                                <div class="reel-caption-wrap">
                                    <p class="reel-title">{{ $short->name }}</p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Fallback Reel 1 -->
                        <div class="reel-card" data-index="0" data-pandal="Santipara 2nd Lane Puja"
                            data-likes="14.2K" data-theme="Swarna Mandir Golden Splendor & Dhaki Rhythms"
                            data-splash="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?auto=format&fit=crop&w=700&q=80"
                            data-video="https://www.pexels.com/download/video/34945624/">
                            <video class="reel-bg"
                                poster="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?auto=format&fit=crop&w=700&q=80"
                                src="https://www.pexels.com/download/video/34945624/" muted loop playsinline></video>
                            <div class="reel-overlay"></div>
                            <div class="reel-play-btn" title="Watch Short">
                                <svg viewBox="0 0 24 24"><polygon points="6,3 20,12 6,21"></polygon></svg>
                            </div>
                            <div class="reel-caption-wrap">
                                <p class="reel-title">Santipara 2nd Lane Puja</p>
                            </div>
                        </div>

                        <!-- Fallback Reel 2 -->
                        <div class="reel-card" data-index="1" data-pandal="Chowkidingee Charali Puja"
                            data-likes="28.9K" data-theme="Neon Crystal Illumination & Modern Laser Art"
                            data-splash="https://images.unsplash.com/photo-1508739773434-c26b3d09e071?auto=format&fit=crop&w=700&q=80"
                            data-video="https://www.pexels.com/download/video/34945624/">
                            <video class="reel-bg"
                                poster="https://images.unsplash.com/photo-1508739773434-c26b3d09e071?auto=format&fit=crop&w=700&q=80"
                                src="https://www.pexels.com/download/video/34945624/" muted loop playsinline></video>
                            <div class="reel-overlay"></div>
                            <div class="reel-play-btn" title="Watch Short">
                                <svg viewBox="0 0 24 24"><polygon points="6,3 20,12 6,21"></polygon></svg>
                            </div>
                            <div class="reel-caption-wrap">
                                <p class="reel-title">Chowkidingee Charali Puja</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Circular Navigation Button -->
            <button class="nav-arrow-btn nav-arrow-next" id="nextReelBtn" aria-label="Next reels">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>

        </div>

    </div>

</section>
