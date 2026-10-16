@if ($event->awards && $event->awards->where('is_active', true)->isNotEmpty())
<!-- Vote for the Best — Sharad Samman 2026 Section -->
<section class="pujas-voting-section" id="pujas" style="border-bottom: 2px solid rgba(255, 204, 0, 0.5);">
    <!-- Embedded Awards to be Presented with slightly darker background -->
    <div class="awards-embedded-box mb-0" id="categories" style="border-bottom: none;">
        <div class="container">
            <!-- Section Title & Festive Divider Motif -->
            <h2 class="awards-main-heading text-white">People's Favorite Awards</h2>

            <div class="festive-divider-wrap" aria-hidden="true">
                <div class="festive-line"></div>
                <div class="festive-center-motif" title="{{ $event->display_name ?? 'Durgo Utsav 2026' }}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2 C13 7 17 11 22 12 C17 13 13 17 12 22 C11 17 7 13 2 12 C7 11 11 7 12 2 Z"
                            fill="#8b131a" />
                        <circle cx="12" cy="12" r="3" fill="#ffcc00" />
                        <circle cx="12" cy="12" r="1.2" fill="#8b131a" />
                    </svg>
                </div>
                <div class="festive-line festive-line-right"></div>w
            </div>

            <!-- Awards Cards Grid -->
            <div class="row g-3 justify-content-center">
                @foreach ($event->awards->where('is_active', true) as $award)
                    @php
                        $isCrown = $loop->first || $award->category === 'Grand Championship' || str_contains(strtolower($award->name), 'overall') || str_contains(strtolower($award->name), 'serar sera');
                        $headerColor = ($loop->iteration % 2 === 0) ? 'dark' : 'crimson';
                        $isPandal = str_contains(strtolower($award->name), 'pandal') || str_contains(strtolower($award->name), 'mandap');
                        $isIdol = str_contains(strtolower($award->name), 'idol') || str_contains(strtolower($award->name), 'pratima');
                    @endphp

                    @if ($isCrown)
                        <!-- CARD: Grand Champion / Best Puja Overall (Crown Header) -->
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="award-card award-card-crown" data-category="{{ $award->display_name ?: $award->name }}"
                                data-header-color="pinnacle-gold">
                                <!-- Pinnacle Best of Best Ribbon Badge -->
                                <div class="pinnacle-ribbon-badge">
                                    <i class="bi bi-stars text-warning me-1"></i>
                                    <span>BEST OF THE BEST • GRAND CHAMPION</span>
                                    <i class="bi bi-stars text-warning ms-1"></i>
                                </div>

                                <!-- Header -->
                                <div class="award-card-header header-crown-pinnacle">
                                    <span class="award-header-icon crown-header-glow">
                                        <!-- Royal Crown SVG -->
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5 16L3 5L8.5 10L12 4L15.5 10L21 5L19 16H5Z"
                                                fill="url(#pinnacleCrownGold_{{ $award->id }})" />
                                            <path d="M19 19C19 19.55 18.55 20 18 20H6C5.45 20 5 19.55 5 19V18H19V19Z"
                                                fill="url(#pinnacleCrownGold_{{ $award->id }})" />
                                            <circle cx="3" cy="4" r="1.5" fill="#ffffff" />
                                            <circle cx="8.5" cy="9" r="1.3" fill="#ffffff" />
                                            <circle cx="12" cy="3" r="1.8" fill="#ffffff" />
                                            <circle cx="15.5" cy="9" r="1.3" fill="#ffffff" />
                                            <circle cx="21" cy="4" r="1.5" fill="#ffffff" />
                                            <circle cx="12" cy="13.5" r="1.5" fill="#dc2626" />
                                            <defs>
                                                <linearGradient id="pinnacleCrownGold_{{ $award->id }}" x1="0%" y1="0%" x2="100%" y2="100%">
                                                    <stop offset="0%" stop-color="#fffbeb" />
                                                    <stop offset="50%" stop-color="#fbbf24" />
                                                    <stop offset="100%" stop-color="#d97706" />
                                                </linearGradient>
                                            </defs>
                                        </svg>
                                    </span>
                                    <h3 class="award-header-title crown-gold-title">{{ $award->display_name ?: $award->name }}</h3>
                                </div>

                                <!-- Body with Crown Winner -->
                                <div
                                    class="award-card-body award-card-body-crown d-flex flex-column align-items-center justify-content-center position-relative">
                                    <!-- Background Sunburst Halo & Sparkles -->
                                    <div class="crown-sunburst-halo" aria-hidden="true"></div>
                                    <span class="crown-sparkle sparkle-top-left" aria-hidden="true">✦</span>
                                    <span class="crown-sparkle sparkle-top-right" aria-hidden="true">✦</span>
                                    <span class="crown-sparkle sparkle-bottom-left" aria-hidden="true">✦</span>
                                    <span class="crown-sparkle sparkle-bottom-right" aria-hidden="true">✦</span>

                                    <div class="medal-unit medal-unit-grand" data-rank="Crown Winner (Grand Champion)"
                                        data-category="{{ $award->display_name ?: $award->name }}"
                                        title="{{ $award->display_name ?: $award->name }} — Crown Winner (Grand Champion)">
                                        <!-- Pedestal with Laurel Wreath framing the Crown -->
                                        <div class="crown-pedestal-wrap">
                                            <!-- Golden Laurel Wreath Backing -->
                                            <svg class="laurel-wreath-svg" width="96" height="88" viewBox="0 0 100 90"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <defs>
                                                    <linearGradient id="laurelLeafGold_{{ $award->id }}" x1="0%" y1="0%" x2="100%" y2="100%">
                                                        <stop offset="0%" stop-color="#fffbeb" />
                                                        <stop offset="35%" stop-color="#fde047" />
                                                        <stop offset="70%" stop-color="#f59e0b" />
                                                        <stop offset="100%" stop-color="#b45309" />
                                                    </linearGradient>
                                                    <linearGradient id="laurelStemGold_{{ $award->id }}" x1="0%" y1="0%" x2="0%" y2="100%">
                                                        <stop offset="0%" stop-color="#fbbf24" />
                                                        <stop offset="100%" stop-color="#b45309" />
                                                    </linearGradient>
                                                </defs>
                                                <!-- Left Laurel Stem -->
                                                <path d="M 47,78 C 30,75 14,58 14,36 C 14,24 20,15 25,10" fill="none"
                                                    stroke="url(#laurelStemGold_{{ $award->id }})" stroke-width="1.8" stroke-linecap="round" />
                                                <!-- Left Leaves -->
                                                <path d="M 40,75 C 34,76 30,72 34,68 C 38,68 40,71 40,75 Z"
                                                    fill="url(#laurelLeafGold_{{ $award->id }})" />
                                                <path d="M 43,72 C 37,70 35,65 40,63 C 44,65 44,69 43,72 Z"
                                                    fill="url(#laurelLeafGold_{{ $award->id }})" />
                                                <path d="M 28,66 C 22,66 18,61 22,57 C 27,57 28,62 28,66 Z"
                                                    fill="url(#laurelLeafGold_{{ $award->id }})" />
                                                <path d="M 31,61 C 25,58 23,52 28,50 C 33,52 33,57 31,61 Z"
                                                    fill="url(#laurelLeafGold_{{ $award->id }})" />
                                                <path d="M 19,53 C 13,51 10,45 15,42 C 20,43 20,49 19,53 Z"
                                                    fill="url(#laurelLeafGold_{{ $award->id }})" />
                                                <path d="M 22,46 C 16,42 16,36 21,35 C 26,38 25,43 22,46 Z"
                                                    fill="url(#laurelLeafGold_{{ $award->id }})" />
                                                <path d="M 15,38 C 10,34 9,28 14,26 C 19,28 18,34 15,38 Z"
                                                    fill="url(#laurelLeafGold_{{ $award->id }})" />
                                                <path d="M 19,30 C 14,25 15,19 20,19 C 24,22 23,27 19,30 Z"
                                                    fill="url(#laurelLeafGold_{{ $award->id }})" />
                                                <path d="M 17,21 C 13,16 16,11 21,11 C 24,14 22,19 17,21 Z"
                                                    fill="url(#laurelLeafGold_{{ $award->id }})" />
                                                <path d="M 24,13 C 21,8 26,4 29,6 C 31,10 28,13 24,13 Z"
                                                    fill="url(#laurelLeafGold_{{ $award->id }})" />

                                                <!-- Right Laurel Stem -->
                                                <path d="M 53,78 C 70,75 86,58 86,36 C 86,24 80,15 75,10" fill="none"
                                                    stroke="url(#laurelStemGold_{{ $award->id }})" stroke-width="1.8" stroke-linecap="round" />
                                                <!-- Right Leaves -->
                                                <path d="M 60,75 C 66,76 70,72 66,68 C 62,68 60,71 60,75 Z"
                                                    fill="url(#laurelLeafGold_{{ $award->id }})" />
                                                <path d="M 57,72 C 63,70 65,65 60,63 C 56,65 56,69 57,72 Z"
                                                    fill="url(#laurelLeafGold_{{ $award->id }})" />
                                                <path d="M 72,66 C 78,66 82,61 78,57 C 73,57 72,62 72,66 Z"
                                                    fill="url(#laurelLeafGold_{{ $award->id }})" />
                                                <path d="M 69,61 C 75,58 77,52 72,50 C 67,52 67,57 69,61 Z"
                                                    fill="url(#laurelLeafGold_{{ $award->id }})" />
                                                <path d="M 81,53 C 87,51 90,45 85,42 C 80,43 80,49 81,53 Z"
                                                    fill="url(#laurelLeafGold_{{ $award->id }})" />
                                                <path d="M 78,46 C 84,42 84,36 79,35 C 74,38 75,43 78,46 Z"
                                                    fill="url(#laurelLeafGold_{{ $award->id }})" />
                                                <path d="M 85,38 C 90,34 91,28 86,26 C 81,28 82,34 85,38 Z"
                                                    fill="url(#laurelLeafGold_{{ $award->id }})" />
                                                <path d="M 81,30 C 86,25 85,19 80,19 C 76,22 77,27 81,30 Z"
                                                    fill="url(#laurelLeafGold_{{ $award->id }})" />
                                                <path d="M 83,21 C 87,16 84,11 79,11 C 76,14 78,19 83,21 Z"
                                                    fill="url(#laurelLeafGold_{{ $award->id }})" />
                                                <path d="M 76,13 C 79,8 74,4 71,6 C 69,10 72,13 76,13 Z"
                                                    fill="url(#laurelLeafGold_{{ $award->id }})" />

                                                <!-- Ribbon Knot at bottom center -->
                                                <path
                                                    d="M 50,77 C 45,77 41,82 43,84.5 C 46,85.5 49,81 50,78.5 C 51,81 54,85.5 57,84.5 C 59,82 55,77 50,77 Z"
                                                    fill="#b91c1c" stroke="#f59e0b" stroke-width="0.8" />
                                                <circle cx="50" cy="77.5" r="2.2" fill="#fbbf24" stroke="#92400e"
                                                    stroke-width="0.6" />
                                            </svg>

                                            <!-- 3D Sovereign Crown SVG Motif -->
                                            <svg class="crown-motif-svg" width="68" height="60" viewBox="0 0 100 85"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <defs>
                                                    <linearGradient id="crownPeakShine_{{ $award->id }}" x1="0%" y1="0%" x2="100%" y2="100%">
                                                        <stop offset="0%" stop-color="#ffffff" />
                                                        <stop offset="30%" stop-color="#fef08a" />
                                                        <stop offset="70%" stop-color="#f59e0b" />
                                                        <stop offset="100%" stop-color="#b45309" />
                                                    </linearGradient>
                                                    <linearGradient id="crownVelvetRed_{{ $award->id }}" x1="0%" y1="0%" x2="0%" y2="100%">
                                                        <stop offset="0%" stop-color="#dc2626" />
                                                        <stop offset="100%" stop-color="#7f1d1d" />
                                                    </linearGradient>
                                                    <linearGradient id="crownBaseGold_{{ $award->id }}" x1="0%" y1="0%" x2="100%" y2="0%">
                                                        <stop offset="0%" stop-color="#b45309" />
                                                        <stop offset="25%" stop-color="#fbbf24" />
                                                        <stop offset="50%" stop-color="#fef08a" />
                                                        <stop offset="75%" stop-color="#f59e0b" />
                                                        <stop offset="100%" stop-color="#92400e" />
                                                    </linearGradient>
                                                    <radialGradient id="pearlShine_{{ $award->id }}" cx="35%" cy="35%" r="65%">
                                                        <stop offset="0%" stop-color="#ffffff" />
                                                        <stop offset="70%" stop-color="#fef3c7" />
                                                        <stop offset="100%" stop-color="#d97706" />
                                                    </radialGradient>
                                                    <radialGradient id="rubyGlow_{{ $award->id }}" cx="35%" cy="35%" r="65%">
                                                        <stop offset="0%" stop-color="#fca5a5" />
                                                        <stop offset="60%" stop-color="#dc2626" />
                                                        <stop offset="100%" stop-color="#7f1d1d" />
                                                    </radialGradient>
                                                    <radialGradient id="emeraldGlow_{{ $award->id }}" cx="35%" cy="35%" r="65%">
                                                        <stop offset="0%" stop-color="#86efac" />
                                                        <stop offset="60%" stop-color="#16a34a" />
                                                        <stop offset="100%" stop-color="#14532d" />
                                                    </radialGradient>
                                                </defs>

                                                <!-- Imperial Velvet Cap Backing -->
                                                <path d="M 24,62 C 23,38 34,22 50,20 C 66,22 77,38 76,62 Z"
                                                    fill="url(#crownVelvetRed_{{ $award->id }})" />
                                                <path d="M 50,20 Q 50,45 50,62" stroke="#991b1b" stroke-width="1.2"
                                                    opacity="0.6" />

                                                <!-- Outer Left Spire -->
                                                <path d="M 20,62 L 15,28 L 27,42 L 32,62 Z" fill="url(#crownPeakShine_{{ $award->id }})" />
                                                <!-- Inner Left Spire -->
                                                <path d="M 29,62 L 32,20 L 41,40 L 42,62 Z" fill="url(#crownPeakShine_{{ $award->id }})" />
                                                <!-- Center Grand Sovereign Spire -->
                                                <path d="M 38,62 L 50,10 L 62,62 Z" fill="url(#crownPeakShine_{{ $award->id }})" />
                                                <!-- Center Spire Highlight Ridge -->
                                                <path d="M 50,10 L 50,62" stroke="#fffbeb" stroke-width="1.6"
                                                    stroke-linecap="round" />
                                                <!-- Inner Right Spire -->
                                                <path d="M 58,62 L 59,40 L 68,20 L 71,62 Z" fill="url(#crownPeakShine_{{ $award->id }})" />
                                                <!-- Outer Right Spire -->
                                                <path d="M 68,62 L 73,42 L 85,28 L 80,62 Z" fill="url(#crownPeakShine_{{ $award->id }})" />

                                                <!-- Finial Pearls on Spire Tips -->
                                                <circle cx="15" cy="27" r="3.2" fill="url(#pearlShine_{{ $award->id }})" />
                                                <circle cx="32" cy="19" r="3.5" fill="url(#pearlShine_{{ $award->id }})" />
                                                <!-- Center Majestic Cross Finial on top of center peak -->
                                                <g transform="translate(50, 9)">
                                                    <circle cx="0" cy="0" r="4.2" fill="url(#pearlShine_{{ $award->id }})" />
                                                    <path d="M 0,-7 L 0,1 M -4,-3 L 4,-3" stroke="#fef08a"
                                                        stroke-width="1.6" stroke-linecap="round" />
                                                    <circle cx="0" cy="-3" r="1.3" fill="#ffffff" />
                                                </g>
                                                <circle cx="68" cy="19" r="3.5" fill="url(#pearlShine_{{ $award->id }})" />
                                                <circle cx="85" cy="27" r="3.2" fill="url(#pearlShine_{{ $award->id }})" />

                                                <!-- Valley Pearls -->
                                                <circle cx="27" cy="42" r="2.2" fill="url(#pearlShine_{{ $award->id }})" />
                                                <circle cx="41" cy="40" r="2.4" fill="url(#pearlShine_{{ $award->id }})" />
                                                <circle cx="59" cy="40" r="2.4" fill="url(#pearlShine_{{ $award->id }})" />
                                                <circle cx="73" cy="42" r="2.2" fill="url(#pearlShine_{{ $award->id }})" />

                                                <!-- Circlet Base Frame -->
                                                <path d="M 16,62 C 30,59 70,59 84,62 L 84,72 C 70,75 30,75 16,72 Z"
                                                    fill="url(#crownBaseGold_{{ $award->id }})" stroke="#78350f" stroke-width="0.8" />
                                                <!-- Upper Beaded Rim -->
                                                <path d="M 16,62 C 30,59 70,59 84,62" stroke="#fef08a" stroke-width="1.4"
                                                    stroke-linecap="round" />
                                                <!-- Lower Beaded Rim -->
                                                <path d="M 16,72 C 30,75 70,75 84,72" stroke="#92400e" stroke-width="1.2"
                                                    stroke-linecap="round" />

                                                <!-- Circlet Inset Jewels: Rubies, Emeralds, Diamond -->
                                                <g transform="translate(0, 0)">
                                                    <circle cx="24" cy="67" r="2.4" fill="url(#rubyGlow_{{ $award->id }})" />
                                                    <polygon points="34,64.8 36.4,67 34,69.2 31.6,67" fill="url(#emeraldGlow_{{ $award->id }})" />
                                                    <circle cx="43" cy="67" r="2.5" fill="url(#rubyGlow_{{ $award->id }})" />
                                                    <!-- Center Brilliant Cut Diamond -->
                                                    <polygon points="50,63.8 53.6,67 50,70.2 46.4,67" fill="#ffffff"
                                                        stroke="#38bdf8" stroke-width="0.6" />
                                                    <circle cx="50" cy="67" r="1.2" fill="#e0f2fe" />
                                                    <circle cx="57" cy="67" r="2.5" fill="url(#rubyGlow_{{ $award->id }})" />
                                                    <polygon points="66,64.8 68.4,67 66,69.2 63.6,67" fill="url(#emeraldGlow_{{ $award->id }})" />
                                                    <circle cx="76" cy="67" r="2.4" fill="url(#rubyGlow_{{ $award->id }})" />
                                                </g>
                                            </svg>
                                        </div>
                                        <div class="crown-badge-pill">
                                            <i class="bi bi-crown-fill text-warning me-1"></i>{{ $award->prize_money_or_award ?: 'GOLD TROPHY' }}
                                        </div>
                                        <div class="crown-subtitle-tag">
                                            {{ $award->description ?: ($award->category ?: 'Maha Puraskar • Grand Champion') }}
                                        </div>
                                        <div class="crown-bottom-stars" aria-hidden="true">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill mx-1 star-center"></i>
                                            <i class="bi bi-star-fill"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Standard Award Card -->
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="award-card" data-category="{{ $award->display_name ?: $award->name }}" data-header-color="{{ $headerColor }}">
                                @if ($award->prize_money_or_award)
                                    <div class="award-top-prize-ribbon {{ $headerColor === 'dark' ? 'award-top-prize-ribbon-dark' : '' }}">
                                        <i class="bi bi-stars text-warning me-1"></i>
                                        <span>{{ $award->prize_money_or_award }}</span>
                                        <i class="bi bi-stars text-warning ms-1"></i>
                                    </div>
                                @endif

                                <!-- Header -->
                                <div class="award-card-header header-{{ $headerColor }}">
                                    <span class="award-header-icon">
                                        @if ($isPandal)
                                            <!-- Pandal / Mandap Architecture SVG -->
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 2L13.8 5.2H10.2L12 2Z" fill="#fbbf24" />
                                                <circle cx="12" cy="2" r="0.9" fill="#ffffff" />
                                                <path d="M5 10.5C5 6.8 8.1 5.2 12 5.2C15.9 5.2 19 6.8 19 10.5L20 12.5H4L5 10.5Z"
                                                    fill="#ffffff" />
                                                <path
                                                    d="M6 12.5V20.5H8.8V15C8.8 13.8 10 12.8 12 12.8C14 12.8 15.2 13.8 15.2 15V20.5H18V12.5"
                                                    fill="#ffffff" fill-opacity="0.9" />
                                                <path d="M10 20.5V16C10 15 10.9 14.2 12 14.2C13.1 14.2 14 15 14 16V20.5H10Z"
                                                    fill="#fbbf24" />
                                                <path d="M3 20.5H21V22H3V20.5Z" fill="#ffffff" />
                                            </svg>
                                        @elseif ($isIdol)
                                            <!-- Durga Pratima Icon SVG -->
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 2 L14 6 L18 4 L16 8 L19 10 L15 11 L12 8 L9 11 L5 10 L8 8 L6 4 L10 6 Z"
                                                    fill="#ffffff" />
                                                <path d="M8 11 C8 16 10 18.5 12 18.5 C14 18.5 16 16 16 11 Z" fill="#ffffff"
                                                    fill-opacity="0.9" />
                                                <path d="M12 11.5 C12.6 12.2 12.6 12.8 12 13.5 C11.4 12.8 11.4 12.2 12 11.5 Z"
                                                    fill="#16181b" />
                                                <ellipse cx="10" cy="14" rx="1.5" ry="0.8" fill="#16181b" />
                                                <ellipse cx="14" cy="14" rx="1.5" ry="0.8" fill="#16181b" />
                                                <path d="M7 21 C9 20 15 20 17 21 L18 22.5 L6 22.5 Z" fill="#ffffff" />
                                            </svg>
                                        @else
                                            <!-- Trophy Icon SVG -->
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M19 4H17V3C17 2.45 16.55 2 16 2H8C7.45 2 7 2.45 7 3V4H5C3.9 4 3 4.9 3 6V8C3 10.55 4.92 12.63 7.39 12.94C8.14 14.15 9.42 15 11 15.34V18H8C7.45 18 7 18.45 7 19C7 19.55 7.45 20 8 20H16C16.55 20 17 19.55 17 19C17 18.45 16.55 18 16 18H13V15.34C14.58 15 15.86 14.15 16.61 12.94C19.08 12.63 21 10.55 21 8V6C21 4.9 20.1 4 19 4ZM5 8V6H7V10.1C5.84 9.65 5 8.92 5 8ZM19 8C19 8.92 18.16 9.65 17 10.1V6H19V8Z" />
                                            </svg>
                                        @endif
                                    </span>
                                    <div class="award-header-title-wrap">
                                        <h3 class="award-header-title">{{ $award->display_name ?: $award->name }}</h3>
                                        <div class="award-header-subtitle">{{ $award->category ?: '' }}</div>
                                    </div>
                                </div>

                                <!-- Body with 3 Medals -->
                                <div class="award-card-body">
                                    <div class="row g-1 text-center">

                                        <!-- 1. Winner (Gold) -->
                                        <div class="col-4">
                                            <div class="medal-unit" data-rank="1st Place (Winner)"
                                                data-category="{{ $award->display_name ?: $award->name }}">
                                                <div class="medal-wrapper">
                                                    <!-- Scalloped Gold Medal SVG -->
                                                    <svg width="52" height="58" viewBox="0 0 70 80" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <!-- Ribbon Tails -->
                                                        <polygon points="26,45 20,78 32,71 36,52" fill="#d97706" />
                                                        <polygon points="44,45 50,78 38,71 34,52" fill="#b45309" />
                                                        <!-- Scalloped Cog Rosette Badge -->
                                                        <g transform="translate(35, 34)">
                                                            <circle cx="0" cy="0" r="28" fill="#f59e0b" />
                                                            <path
                                                                d="M0 -30 L4 -26 L9 -30 L11 -24 L17 -26 L17 -20 L23 -20 L21 -14 L27 -12 L23 -7 L28 -3 L23 2 L26 7 L20 11 L22 17 L15 19 L15 25 L8 25 L6 30 L0 28 L-6 30 L-8 25 L-15 25 L-15 19 L-22 17 L-20 11 L-26 7 L-23 2 L-28 -3 L-23 -7 L-27 -12 L-21 -14 L-23 -20 L-17 -20 L-17 -26 L-11 -24 L-9 -30 L-4 -26 Z"
                                                                fill="#fde047" />
                                                            <circle cx="0" cy="0" r="22" fill="#b45309" />
                                                            <circle cx="0" cy="0" r="18" fill="none" stroke="#fef08a"
                                                                stroke-width="1.2" stroke-dasharray="2 1.5" />
                                                            <text x="0" y="7" font-family="'Outfit', sans-serif"
                                                                font-size="20" font-weight="900" fill="#ffffff"
                                                                text-anchor="middle">1</text>
                                                        </g>
                                                    </svg>
                                                </div>
                                                <div class="award-position-title">1<sup>st</sup></div>
                                            </div>
                                        </div>

                                        <!-- 2. Runner Up (Silver) -->
                                        <div class="col-4">
                                            <div class="medal-unit" data-rank="Runner Up (2nd Place)"
                                                data-category="{{ $award->display_name ?: $award->name }}">
                                                <div class="medal-wrapper">
                                                    <!-- Scalloped Silver Medal SVG -->
                                                    <svg width="52" height="58" viewBox="0 0 70 80" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <polygon points="26,45 20,78 32,71 36,52" fill="#94a3b8" />
                                                        <polygon points="44,45 50,78 38,71 34,52" fill="#64748b" />
                                                        <g transform="translate(35, 34)">
                                                            <circle cx="0" cy="0" r="28" fill="#cbd5e1" />
                                                            <path
                                                                d="M0 -30 L4 -26 L9 -30 L11 -24 L17 -26 L17 -20 L23 -20 L21 -14 L27 -12 L23 -7 L28 -3 L23 2 L26 7 L20 11 L22 17 L15 19 L15 25 L8 25 L6 30 L0 28 L-6 30 L-8 25 L-15 25 L-15 19 L-22 17 L-20 11 L-26 7 L-23 2 L-28 -3 L-23 -7 L-27 -12 L-21 -14 L-23 -20 L-17 -20 L-17 -26 L-11 -24 L-9 -30 L-4 -26 Z"
                                                                fill="#e2e8f0" />
                                                            <circle cx="0" cy="0" r="22" fill="#64748b" />
                                                            <circle cx="0" cy="0" r="18" fill="none" stroke="#ffffff"
                                                                stroke-width="1.2" stroke-dasharray="2 1.5" />
                                                            <text x="0" y="7" font-family="'Outfit', sans-serif"
                                                                font-size="20" font-weight="900" fill="#ffffff"
                                                                text-anchor="middle">2</text>
                                                        </g>
                                                    </svg>
                                                </div>
                                                <div class="award-position-title">2<sup>nd</sup></div>
                                            </div>
                                        </div>

                                        <!-- 3. Second Runner Up (Bronze) -->
                                        <div class="col-4">
                                            <div class="medal-unit" data-rank="Second Runner Up (3rd Place)"
                                                data-category="{{ $award->display_name ?: $award->name }}">
                                                <div class="medal-wrapper">
                                                    <!-- Scalloped Bronze Medal SVG -->
                                                    <svg width="52" height="58" viewBox="0 0 70 80" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <polygon points="26,45 20,78 32,71 36,52" fill="#c2410c" />
                                                        <polygon points="44,45 50,78 38,71 34,52" fill="#9a3412" />
                                                        <g transform="translate(35, 34)">
                                                            <circle cx="0" cy="0" r="28" fill="#ea580c" />
                                                            <path
                                                                d="M0 -30 L4 -26 L9 -30 L11 -24 L17 -26 L17 -20 L23 -20 L21 -14 L27 -12 L23 -7 L28 -3 L23 2 L26 7 L20 11 L22 17 L15 19 L15 25 L8 25 L6 30 L0 28 L-6 30 L-8 25 L-15 25 L-15 19 L-22 17 L-20 11 L-26 7 L-23 2 L-28 -3 L-23 -7 L-27 -12 L-21 -14 L-23 -20 L-17 -20 L-17 -26 L-11 -24 L-9 -30 L-4 -26 Z"
                                                                fill="#fdba74" />
                                                            <circle cx="0" cy="0" r="22" fill="#9a3412" />
                                                            <circle cx="0" cy="0" r="18" fill="none" stroke="#ffedd5"
                                                                stroke-width="1.2" stroke-dasharray="2 1.5" />
                                                            <text x="0" y="7" font-family="'Outfit', sans-serif"
                                                                font-size="20" font-weight="900" fill="#ffffff"
                                                                text-anchor="middle">3</text>
                                                        </g>
                                                    </svg>
                                                </div>
                                                <div class="award-position-title">3<sup>rd</sup></div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- Category Focus Strip -->
                                    @if ($award->description || $award->category)
                                        <div class="award-card-prize-footer">
                                            <i class="bi bi-flower1 text-danger me-1"></i>
                                            <span>{{ $award->description ?: $award->category }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif