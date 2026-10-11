<div class="awards-embedded-box mb-5 " id="categories">
    <div class="container">
        <!-- Section Title & Festive Divider Motif -->
        <h2 class="awards-main-heading text-white">Awards to be Presented</h2>

        <div class="festive-divider-wrap" aria-hidden="true">
            <div class="festive-line"></div>
            <div class="festive-center-motif" title="{{ $event->display_name ?? 'Durgo Utsav 2026' }}">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2 C13 7 17 11 22 12 C17 13 13 17 12 22 C11 17 7 13 2 12 C7 11 11 7 12 2 Z"
                        fill="#8b131a" />
                    <circle cx="12" cy="12" r="3" fill="#ffcc00" />
                    <circle cx="12" cy="12" r="1.2" fill="#8b131a" />
                </svg>
            </div>
            <div class="festive-line festive-line-right"></div>
        </div>

        <!-- Awards Cards Grid -->
        <div class="row g-3 justify-content-center">
            @if ($event->awards && $event->awards->isNotEmpty())
                @php
                    $headerThemes = ['crimson', 'dark', 'gold'];
                @endphp
                @foreach ($event->awards as $index => $award)
                    @php
                        $theme = $headerThemes[$index % 3];
                        $headerClass = 'header-' . $theme;
                        $firstWinner = $event->winners->where('award_id', $award->id)->first(fn($w) => ($w->rank_order?->value ?? $w->rank_order) === '1st');
                        $secondWinner = $event->winners->where('award_id', $award->id)->first(fn($w) => ($w->rank_order?->value ?? $w->rank_order) === '2nd');
                        $thirdWinner = $event->winners->where('award_id', $award->id)->first(fn($w) => ($w->rank_order?->value ?? $w->rank_order) === '3rd');
                    @endphp
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="award-card h-100" data-category="{{ $award->display_name ?: $award->name }}" data-header-color="{{ $theme }}">
                            <!-- Header -->
                            <div class="award-card-header {{ $headerClass }}">
                                <span class="award-header-icon">
                                    @if ($index % 3 === 0)
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19 4H17V3C17 2.45 16.55 2 16 2H8C7.45 2 7 2.45 7 3V4H5C3.9 4 3 4.9 3 6V8C3 10.55 4.92 12.63 7.39 12.94C8.14 14.15 9.42 15 11 15.34V18H8C7.45 18 7 18.45 7 19C7 19.55 7.45 20 8 20H16C16.55 20 17 19.55 17 19C17 18.45 16.55 18 16 18H13V15.34C14.58 15 15.86 14.15 16.61 12.94C19.08 12.63 21 10.55 21 8V6C21 4.9 20.1 4 19 4ZM5 8V6H7V10.1C5.84 9.65 5 8.92 5 8ZM19 8C19 8.92 18.16 9.65 17 10.1V6H19V8Z" />
                                        </svg>
                                    @elseif ($index % 3 === 1)
                                        <i class="bi bi-flower1 fs-5"></i>
                                    @else
                                        <i class="bi bi-lightbulb-fill fs-5"></i>
                                    @endif
                                </span>
                                <div>
                                    <h3 class="award-header-title mb-0">{{ $award->display_name ?: $award->name }}</h3>
                                    @if ($award->category)
                                        <span class="badge bg-black bg-opacity-25 rounded-pill px-2 py-1" style="font-size: 0.65rem; font-weight: 500;">
                                            {{ $award->category }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Body with 3 Medals -->
                            <div class="award-card-body">
                                <div class="row g-1 text-center">

                                    <!-- 1. Winner (Gold) -->
                                    <div class="col-4">
                                        <div class="medal-unit" data-rank="1st Place (Winner)" data-category="{{ $award->display_name ?: $award->name }}">
                                            <div class="medal-wrapper">
                                                <svg width="52" height="58" viewBox="0 0 70 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <polygon points="26,45 20,78 32,71 36,52" fill="#d97706" />
                                                    <polygon points="44,45 50,78 38,71 34,52" fill="#b45309" />
                                                    <g transform="translate(35, 34)">
                                                        <circle cx="0" cy="0" r="28" fill="#f59e0b" />
                                                        <path d="M0 -30 L4 -26 L9 -30 L11 -24 L17 -26 L17 -20 L23 -20 L21 -14 L27 -12 L23 -7 L28 -3 L23 2 L26 7 L20 11 L22 17 L15 19 L15 25 L8 25 L6 30 L0 28 L-6 30 L-8 25 L-15 25 L-15 19 L-22 17 L-20 11 L-26 7 L-23 2 L-28 -3 L-23 -7 L-27 -12 L-21 -14 L-23 -20 L-17 -20 L-17 -26 L-11 -24 L-9 -30 L-4 -26 Z" fill="#fbbf24" />
                                                        <circle cx="0" cy="0" r="22" fill="#d97706" />
                                                        <circle cx="0" cy="0" r="18" fill="none" stroke="#fef08a" stroke-width="1.2" stroke-dasharray="2 1.5" />
                                                        <text x="0" y="7" font-family="'Outfit', sans-serif" font-size="20" font-weight="900" fill="#ffffff" text-anchor="middle">1</text>
                                                    </g>
                                                </svg>
                                            </div>
                                            <div class="award-position-title">Winner</div>
                                            <div class="award-tba-status text-truncate px-1" title="{{ $firstWinner?->participant?->display_name ?: ($firstWinner?->participant?->name ?? 'TBA') }}">
                                                {{ $firstWinner ? ($firstWinner->participant?->display_name ?: $firstWinner->participant?->name) : 'TBA' }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 2. Runner Up (Silver) -->
                                    <div class="col-4">
                                        <div class="medal-unit" data-rank="Runner Up (2nd Place)" data-category="{{ $award->display_name ?: $award->name }}">
                                            <div class="medal-wrapper">
                                                <svg width="52" height="58" viewBox="0 0 70 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <polygon points="26,45 20,78 32,71 36,52" fill="#94a3b8" />
                                                    <polygon points="44,45 50,78 38,71 34,52" fill="#64748b" />
                                                    <g transform="translate(35, 34)">
                                                        <circle cx="0" cy="0" r="28" fill="#cbd5e1" />
                                                        <path d="M0 -30 L4 -26 L9 -30 L11 -24 L17 -26 L17 -20 L23 -20 L21 -14 L27 -12 L23 -7 L28 -3 L23 2 L26 7 L20 11 L22 17 L15 19 L15 25 L8 25 L6 30 L0 28 L-6 30 L-8 25 L-15 25 L-15 19 L-22 17 L-20 11 L-26 7 L-23 2 L-28 -3 L-23 -7 L-27 -12 L-21 -14 L-23 -20 L-17 -20 L-17 -26 L-11 -24 L-9 -30 L-4 -26 Z" fill="#e2e8f0" />
                                                        <circle cx="0" cy="0" r="22" fill="#64748b" />
                                                        <circle cx="0" cy="0" r="18" fill="none" stroke="#ffffff" stroke-width="1.2" stroke-dasharray="2 1.5" />
                                                        <text x="0" y="7" font-family="'Outfit', sans-serif" font-size="20" font-weight="900" fill="#ffffff" text-anchor="middle">2</text>
                                                    </g>
                                                </svg>
                                            </div>
                                            <div class="award-position-title">Runner Up</div>
                                            <div class="award-tba-status text-truncate px-1" title="{{ $secondWinner?->participant?->display_name ?: ($secondWinner?->participant?->name ?? 'TBA') }}">
                                                {{ $secondWinner ? ($secondWinner->participant?->display_name ?: $secondWinner->participant?->name) : 'TBA' }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 3. Second Runner Up (Bronze) -->
                                    <div class="col-4">
                                        <div class="medal-unit" data-rank="Second Runner Up (3rd Place)" data-category="{{ $award->display_name ?: $award->name }}">
                                            <div class="medal-wrapper">
                                                <svg width="52" height="58" viewBox="0 0 70 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <polygon points="26,45 20,78 32,71 36,52" fill="#c2410c" />
                                                    <polygon points="44,45 50,78 38,71 34,52" fill="#9a3412" />
                                                    <g transform="translate(35, 34)">
                                                        <circle cx="0" cy="0" r="28" fill="#ea580c" />
                                                        <path d="M0 -30 L4 -26 L9 -30 L11 -24 L17 -26 L17 -20 L23 -20 L21 -14 L27 -12 L23 -7 L28 -3 L23 2 L26 7 L20 11 L22 17 L15 19 L15 25 L8 25 L6 30 L0 28 L-6 30 L-8 25 L-15 25 L-15 19 L-22 17 L-20 11 L-26 7 L-23 2 L-28 -3 L-23 -7 L-27 -12 L-21 -14 L-23 -20 L-17 -20 L-17 -26 L-11 -24 L-9 -30 L-4 -26 Z" fill="#fdba74" />
                                                        <circle cx="0" cy="0" r="22" fill="#9a3412" />
                                                        <circle cx="0" cy="0" r="18" fill="none" stroke="#ffedd5" stroke-width="1.2" stroke-dasharray="2 1.5" />
                                                        <text x="0" y="7" font-family="'Outfit', sans-serif" font-size="20" font-weight="900" fill="#ffffff" text-anchor="middle">3</text>
                                                    </g>
                                                </svg>
                                            </div>
                                            <div class="award-position-title">Second Runner Up</div>
                                            <div class="award-tba-status text-truncate px-1" title="{{ $thirdWinner?->participant?->display_name ?: ($thirdWinner?->participant?->name ?? 'TBA') }}">
                                                {{ $thirdWinner ? ($thirdWinner->participant?->display_name ?: $thirdWinner->participant?->name) : 'TBA' }}
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                @if ($award->prize_money_or_award)
                                    <div class="text-center mt-3 pt-2 border-top border-light-subtle">
                                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill px-3 py-1" style="font-size: 0.72rem;">
                                            <i class="bi bi-gift-fill text-warning me-1"></i> {{ $award->prize_money_or_award }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
