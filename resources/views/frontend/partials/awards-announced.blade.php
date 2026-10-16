@if (isset($publishedWinners) && $publishedWinners->isNotEmpty())
    @php
        $winnersByAward = $publishedWinners->groupBy('award_id');

        $getRankValue = fn($w) => $w?->rank_order instanceof \BackedEnum ? $w->rank_order->value : (string) $w?->rank_order;
        $isRank1st = fn($w) => in_array($getRankValue($w), ['1st', 'First', 'Gold'], true);
        $isRank2nd = fn($w) => in_array($getRankValue($w), ['2nd', 'Second', 'Silver'], true);
        $isRank3rd = fn($w) => in_array($getRankValue($w), ['3rd', 'Third', 'Bronze'], true);

        $resolveLoc = function ($p) {
            if (!$p)
                return 'Dibrugarh';
            $loc = $p->locality?->name ?? ($p->getRawOriginal('locality') ?: '');
            $zn = $p->zone?->name ?? ($p->getRawOriginal('zone') ?: 'Dibrugarh');
            return trim(($loc ? $loc . ', ' : '') . $zn);
        };

        $resolveImg = function ($p, $fallbackSlot = null) {
            if (!$p)
                return vasset('event-assets/images/hero.png');
            if ($fallbackSlot === 'image_1' && $p->image_1_url)
                return $p->image_1_url;
            if ($fallbackSlot === 'image_2' && $p->image_2_url)
                return $p->image_2_url;
            return $p->primary_image_url ?: vasset('event-assets/images/hero.png');
        };

        // FIXED SINGLE SECTION: Strictly the award category that is not available for voting and has no rank.
        // No other category can take its position!
        $fixedPinnacleWinner = $publishedWinners->first(function ($w) {
            return $w->award && !$w->award->is_voting_allowed;
        });
        $fixedPinnacleAward = $fixedPinnacleWinner?->award;

        // Other categories are strictly separate; none can take the fixed first section
        $otherAwards = $winnersByAward->reject(fn($group, $awardId) => $fixedPinnacleAward && (int) $awardId === (int) $fixedPinnacleAward->id);

        $totalVotesCount = isset($participants)
            ? $participants->sum('votes_count')
            : $publishedWinners->sum(fn($w) => $w->participant?->votes_count ?? 0);
        $totalAwardsCount = $event->awards ? $event->awards->where('is_active', true)->count() : $winnersByAward->count();
    @endphp

    <!-- Official Awards Announced / Winners Showcase Section -->
    <section class="awards-announced-section py-5" id="awards-announced">
        <div class="container py-3">
            <!-- Section Header Box (Clickable redirect to All Pujas) -->
            <a href="{{ isset($event) && $event->slug ? route('events.all-pujas', ['event' => $event->slug]) : route('home') }}"
                class="awards-announced-header-box text-center d-block text-decoration-none mb-5"
                title="Click to view all Dibrugarh Durga Pujas">
                <!-- Top Corner Click Indicator Badge -->
                <div class="awards-click-corner-badge" aria-hidden="true">
                    <span>View All Pujas</span>
                    <i class="bi bi-arrow-up-right-circle-fill ms-1"></i>
                </div>

                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3 shadow-sm">
                    OFFICIAL ANNOUNCEMENT • DIBRUGARH DURGOTSAV {{ $event->year ?? date('Y') }}
                </span>
                <h2 class="display-6 fw-bold mb-2 text-white">
                    🏆 People's Favorite Awards - Voting Live
                    <i class="bi bi-arrow-right-circle-fill text-warning ms-1 click-arrow-icon" aria-hidden="true"></i>
                </h2>
                <p class="lead text-white-50 mx-auto mb-4" style="max-width: 780px; font-size: 1.05rem;">
                    Select the puja you wish to vote for and select the best thing you liked about the selected puja. You
                    cannot vote for any other puja once you vote for a particular puja. So choose the puja accordingly.
                    <br><br>
                    (Voting Live - 17th October 12 noon to 20th October 12 noon)
                </p>


                <!-- Key Stats Bar -->
                <div class="d-flex flex-wrap justify-content-center gap-3 pt-2 border-top border-warning border-opacity-25">
                    <!--<div class="px-3 py-1 rounded-pill bg-white bg-opacity-10 text-white small">-->
                    <!--    <i class="bi bi-check-circle-fill text-warning me-1"></i>-->
                    <!--    <strong>{{ number_format($totalVotesCount > 0 ? $totalVotesCount : 48920) }}+</strong> Verified-->
                    <!--    Votes Cast-->
                    <!--</div>-->
                    <!--<div class="px-3 py-1 rounded-pill bg-white bg-opacity-10 text-white small">-->
                    <!--    <i class="bi bi-award-fill text-warning me-1"></i> <strong>{{ $totalAwardsCount ?: 4 }}-->
                    <!--        Flagship</strong> Award-->
                    <!--    Categories-->
                    <!--</div>-->
                    <!--<div class="px-3 py-1 rounded-pill bg-white bg-opacity-10 text-white small">-->
                    <!--    <i class="bi bi-shield-check text-warning me-1"></i> <strong>Jury Certified</strong> Results-->
                    <!--</div>-->
                </div>
            </a>

            @if ($fixedPinnacleWinner && $fixedPinnacleAward)
                @php
                    $pPart = $fixedPinnacleWinner->participant;
                    $pLocation = $resolveLoc($pPart);
                    $pImg = $resolveImg($pPart);
                    $pPrize = $fixedPinnacleAward->prize_money_or_award ?: '';
                    $pRankLabel = $fixedPinnacleWinner->rank_order instanceof \App\Enums\WinnerRank
                        ? $fixedPinnacleWinner->rank_order->label()
                        : ($fixedPinnacleWinner->rank_order ?: '');
                @endphp
                <!-- FIXED FIRST SINGLE SECTION: NON-VOTING UNRANKED GRAND CHAMPION -->
                <div class="category-crown-wrapper mb-5 p-3 p-md-4 rounded-4 position-relative">
                    <div
                        class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 pb-2 border-bottom border-warning border-opacity-75">
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                {{ $fixedPinnacleAward->display_name ?: $fixedPinnacleAward->name }} — Announced Winner

                            </h3>
                        </div>

                    </div>

                    <div class="row justify-content-center">
                        <!-- Crown Grand Champion Winner (Horizontal Side-by-Side Card) -->
                        <div class="col-12 col-lg-10 col-xl-9">
                            <div class="winner-podium-card award-card-crown winner-horizontal h-100 position-relative">
                                <!-- Pinnacle Best of Best Top Ribbon Badge -->
                                <div class="pinnacle-ribbon-badge">
                                    <i class="bi bi-stars text-warning me-1"></i>
                                    <span>{{$fixedPinnacleAward->display_name}}</span>
                                    <i class="bi bi-stars text-warning ms-1"></i>
                                </div>

                                <div class="row g-0 align-items-stretch">
                                    <!-- Left Column: Photo, Overlay & Badges -->
                                    <div class="col-12 col-md-5 position-relative">
                                        <div class="winner-img-wrap h-100">
                                            <img src="{{ $pImg }}" alt="{{ $pPart?->display_name ?: $pPart?->name }}">
                                            <!-- <span class="winner-rank-badge badge-gold shadow-sm">
                                                                                                                                                <i class="bi bi-stars text-danger me-1"></i> {{ $pRankLabel ?: '' }}
                                                                                                                                            </span> -->
                                            <div class="winner-img-overlay d-flex justify-content-between align-items-end">
                                                <!-- <span class="winner-vote-pill text-warning">
                                                                                                                                                                            <i class="bi bi-patch-check-fill me-1"></i> Jury Certified
                                                                                                                                                                        </span> -->
                                                <!-- <span class="badge bg-warning text-dark fw-bold">
                                                                                                                                                                            <i class="bi bi-trophy-fill me-1"></i>
                                                                                                                                                                        </span> -->
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Column: Accolades & Winner Info -->
                                    <div
                                        class="col-12 col-md-7 d-flex flex-column justify-content-center p-3 p-md-4 position-relative overflow-hidden">
                                        <!-- Background Sunburst Halo & Sparkles -->
                                        <div class="crown-sunburst-halo" aria-hidden="true"></div>
                                        <span class="crown-sparkle sparkle-top-left" aria-hidden="true">✦</span>
                                        <span class="crown-sparkle sparkle-top-right" aria-hidden="true">✦</span>
                                        <span class="crown-sparkle sparkle-bottom-left" aria-hidden="true">✦</span>
                                        <span class="crown-sparkle sparkle-bottom-right" aria-hidden="true">✦</span>

                                        <div class="position-relative" style="z-index: 2;">
                                            <!-- Winner Header: Medallion + Title Details -->
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                <div class="winner-trophy-ring flex-shrink-0 shadow"
                                                    style="width: 56px; height: 56px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 50%, #450a0a 100%); border: 2.5px solid #fbbf24; box-shadow: 0 4px 15px rgba(185, 28, 28, 0.4);">
                                                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M5 16L3 5L8.5 10L12 4L15.5 10L21 5L19 16H5Z"
                                                            fill="url(#pinnacleRingCrownFixed)" stroke="#78350f"
                                                            stroke-width="0.5" />
                                                        <path
                                                            d="M19 19C19 19.55 18.55 20 18 20H6C5.45 20 5 19.55 5 19V18H19V19Z"
                                                            fill="url(#pinnacleRingCrownFixed)" stroke="#78350f"
                                                            stroke-width="0.5" />
                                                        <circle cx="3" cy="4" r="1.6" fill="#ffffff" />
                                                        <circle cx="8.5" cy="9" r="1.4" fill="#ffffff" />
                                                        <circle cx="12" cy="3" r="2.0" fill="#ffffff" />
                                                        <circle cx="15.5" cy="9" r="1.4" fill="#ffffff" />
                                                        <circle cx="21" cy="4" r="1.6" fill="#ffffff" />
                                                        <circle cx="12" cy="13.5" r="1.8" fill="#fbbf24" stroke="#78350f"
                                                            stroke-width="0.5" />
                                                        <defs>
                                                            <linearGradient id="pinnacleRingCrownFixed" x1="0%" y1="0%"
                                                                x2="100%" y2="100%">
                                                                <stop offset="0%" stop-color="#fffbeb" />
                                                                <stop offset="35%" stop-color="#fef08a" />
                                                                <stop offset="70%" stop-color="#f59e0b" />
                                                                <stop offset="100%" stop-color="#d97706" />
                                                            </linearGradient>
                                                        </defs>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <!-- <div class="crown-badge-pill mb-1 d-inline-flex align-items-center">
                                                                                                                                                                                                                            <i class="bi bi-stars text-warning me-1"></i>MAHA PURASKAR •
                                                                                                                                                                                                                            GRAND CHAMPION
                                                                                                                                                                                                                        </div> -->
                                                    <h4 class="fw-bold mb-1 text-dark"
                                                        style="font-size: clamp(1.25rem, 2.5vw, 1.5rem);">
                                                        {{ $pPart?->display_name ?: $pPart?->name }}
                                                    </h4>
                                                    <p class="text-muted small mb-0 d-flex flex-wrap align-items-center gap-2">
                                                        <span><i
                                                                class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $pLocation }}</span>
                                                        <x-gmap-button :participant="$pPart" :location="$pLocation" />
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Accolades / Highlights Bar -->
                                            <div
                                                class="d-flex flex-wrap gap-2 pt-2 border-top border-warning border-opacity-50">
                                                <!--<span-->
                                                <!--    class="badge bg-warning bg-opacity-25 text-dark border border-warning rounded-pill px-3 py-1 small fw-semibold">-->
                                                <!--    <i class="bi bi-trophy-fill text-danger me-1"></i>{{ $pPrize }}-->
                                                <!--</span>-->
                                                <!--<span-->
                                                <!--    class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1 small fw-semibold">-->
                                                <!--    <i class="bi bi-shield-check me-1"></i>Jury Certified Assessment-->
                                                <!--</span>-->
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 small fw-semibold">
                                                    <i
                                                        class="bi bi-stars me-1"></i>{{ $fixedPinnacleAward->display_name ?: '' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ALL OTHER AWARDS: RENDER IN THEIR OWN CATEGORY SECTIONS (NEVER IN THE FIRST SINGLE SECTION) --}}
            @foreach ($otherAwards as $awardId => $awardWinners)
                @php
                    $award = $awardWinners->first()?->award;
                    if (!$award)
                        continue;

                    $claimedIds = [];
                    $goldWinner = $awardWinners->first($isRank1st);
                    if ($goldWinner)
                        $claimedIds[] = $goldWinner->id;

                    $silverWinner = $awardWinners->first(fn($w) => !in_array($w->id, $claimedIds) && $isRank2nd($w));
                    if ($silverWinner)
                        $claimedIds[] = $silverWinner->id;

                    $bronzeWinner = $awardWinners->first(fn($w) => !in_array($w->id, $claimedIds) && $isRank3rd($w));
                    if ($bronzeWinner)
                        $claimedIds[] = $bronzeWinner->id;

                    $unassigned = $awardWinners->reject(fn($w) => in_array($w->id, $claimedIds))->values();

                    if (!$goldWinner && $unassigned->isNotEmpty()) {
                        $goldWinner = $unassigned->shift();
                    }
                    if (!$silverWinner && $unassigned->isNotEmpty()) {
                        $silverWinner = $unassigned->shift();
                    }
                    if (!$bronzeWinner && $unassigned->isNotEmpty()) {
                        $bronzeWinner = $unassigned->shift();
                    }
                    $remainingWinners = $unassigned;

                    $awardText = strtolower(($award->name ?? '') . ' ' . ($award->display_name ?? '') . ' ' . ($award->category ?? ''));
                    if (str_contains($awardText, 'light') || str_contains($awardText, 'aalok') || str_contains($awardText, 'illumination')) {
                        $categoryIcon = 'bi-brightness-high-fill text-warning';
                        $categoryBorder = 'border-warning border-opacity-50';
                        $ringIconGold = 'bi-lightbulb-fill';
                        $ringIconSilver = 'bi-brightness-high';
                        $ringIconBronze = 'bi-sun';
                    } elseif (str_contains($awardText, 'idol') || str_contains($awardText, 'pratima')) {
                        $categoryIcon = 'bi-flower1 text-warning';
                        $categoryBorder = 'border-warning border-opacity-50';
                        $ringIconGold = 'bi-flower1';
                        $ringIconSilver = 'bi-palette';
                        $ringIconBronze = 'bi-brush';
                    } elseif (str_contains($awardText, 'pandal') || str_contains($awardText, 'mandap') || str_contains($awardText, 'eco')) {
                        $categoryIcon = 'bi-palette-fill text-primary';
                        $categoryBorder = 'border-primary border-opacity-25';
                        $ringIconGold = 'bi-bank';
                        $ringIconSilver = 'bi-palette';
                        $ringIconBronze = 'bi-shield-check';
                    } else {
                        $categoryIcon = 'bi-star-fill text-warning';
                        $categoryBorder = 'border-danger border-opacity-25';
                        $ringIconGold = 'bi-trophy-fill';
                        $ringIconSilver = 'bi-award';
                        $ringIconBronze = 'bi-star';
                    }
                @endphp

                <div class="mb-5">
                    <div
                        class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 pb-2 border-bottom {{ $categoryBorder }}">
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="fw-bold mb-0 text-dark">{{ $award->display_name ?: $award->name }}</h3>
                        </div>
                        <span class="text-muted small">
                            <i class="bi {{ $categoryIcon }} me-1"></i>
                            {{ $award->category ?: ($award->description ?: '') }}
                        </span>
                    </div>

                    <div class="row g-4 justify-content-center">
                        @if ($goldWinner)
                            @php
                                $gPart = $goldWinner->participant;
                                $gRankLabel = $goldWinner->rank_order instanceof \App\Enums\WinnerRank
                                    ? $goldWinner->rank_order->label()
                                    : ($goldWinner->rank_order ?: '');
                            @endphp
                            <!-- 1st Place Gold Winner -->
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="winner-podium-card winner-gold h-100">
                                    <span class="winner-rank-badge badge-gold">
                                        <i class="bi bi-trophy-fill me-1"></i> {{ $gRankLabel }}
                                    </span>
                                    <div class="winner-img-wrap">
                                        <img src="{{ $resolveImg($gPart) }}" alt="{{ $gPart?->display_name ?: $gPart?->name }}">
                                        <div class="winner-img-overlay d-flex justify-content-between align-items-end">
                                            <!-- <span class="winner-vote-pill text-warning">
                                                                                                                                                                        <i class="bi bi-fire me-1"></i>
                                                                                                                                                                        {{ $gPart?->votes_count ? number_format($gPart->votes_count) . ' Votes' : 'Champion' }}
                                                                                                                                                                    </span> -->
                                            <!-- <span class="badge bg-warning text-dark fw-bold">GOLD MEDAL</span> -->
                                        </div>
                                    </div>
                                    <div class="p-3 text-center">
                                        <div class="mb-2">
                                            <div class="winner-trophy-ring ring-gold mx-auto">
                                                <i class="bi {{ $ringIconGold }}"></i>
                                            </div>
                                        </div>
                                        <h4 class="fw-bold mb-1 text-dark">{{ $gPart?->display_name ?: $gPart?->name }}</h4>
                                        <p
                                            class="text-muted small mb-2 d-flex flex-wrap align-items-center justify-content-center gap-2">
                                            <span><i
                                                    class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $resolveLoc($gPart) }}</span>
                                            <x-gmap-button :participant="$gPart" :location="$resolveLoc($gPart)" />
                                        </p>
                                        <div class="p-2 rounded bg-warning bg-opacity-10 text-dark small fw-medium">
                                            <i class="bi bi-award-fill text-warning me-1"></i>
                                            {{ $gPart?->puja_theme ? 'Theme: ' . $gPart->puja_theme : ($award->description ?: 'Winner of ' . ($award->display_name ?: $award->name)) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($silverWinner)
                            @php
                                $sPart = $silverWinner->participant;
                                $sRankLabel = $silverWinner->rank_order instanceof \App\Enums\WinnerRank
                                    ? $silverWinner->rank_order->label()
                                    : ($silverWinner->rank_order ?: '2nd Place • Runner-Up');
                            @endphp
                            <!-- 2nd Place Silver Runner-up -->
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="winner-podium-card winner-silver h-100">
                                    <span class="winner-rank-badge badge-silver">
                                        <i class="bi bi-award-fill me-1"></i> {{ $sRankLabel }}
                                    </span>
                                    <div class="winner-img-wrap">
                                        <img src="{{ $resolveImg($sPart, 'image_1') }}"
                                            alt="{{ $sPart?->display_name ?: $sPart?->name }}">
                                        <div class="winner-img-overlay d-flex justify-content-between align-items-end">
                                            <!-- <span class="winner-vote-pill text-light">
                                                                                                                                                            <i class="bi bi-hand-thumbs-up me-1"></i>
                                                                                                                                                            {{ $sPart?->votes_count ? number_format($sPart->votes_count) . ' Votes' : 'Runner-Up' }}
                                                                                                                                                        </span> -->
                                            <!-- <span class="badge bg-secondary text-white fw-bold">SILVER MEDAL</span> -->
                                        </div>
                                    </div>
                                    <div class="p-3 text-center">
                                        <div class="mb-2">
                                            <div class="winner-trophy-ring ring-silver mx-auto">
                                                <i class="bi {{ $ringIconSilver }}"></i>
                                            </div>
                                        </div>
                                        <h4 class="fw-bold mb-1 text-dark">{{ $sPart?->display_name ?: $sPart?->name }}</h4>
                                        <p
                                            class="text-muted small mb-2 d-flex flex-wrap align-items-center justify-content-center gap-2">
                                            <span><i
                                                    class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $resolveLoc($sPart) }}</span>
                                            <x-gmap-button :participant="$sPart" :location="$resolveLoc($sPart)" />
                                        </p>

                                        @if($sPart?->puja_theme)
                                            <div class="p-2 rounded bg-light text-muted small fw-medium">
                                                <i class="bi bi-check-circle-fill text-secondary me-1"></i>
                                                {{ $sPart?->puja_theme ? 'Theme: ' . $sPart->puja_theme : ' ' }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($bronzeWinner)
                            @php
                                $bPart = $bronzeWinner->participant;
                                $bRankLabel = $bronzeWinner->rank_order instanceof \App\Enums\WinnerRank
                                    ? $bronzeWinner->rank_order->label()
                                    : ($bronzeWinner->rank_order ?: '');
                            @endphp
                            <!-- 3rd Place Bronze Runner-up -->
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="winner-podium-card winner-bronze h-100">
                                    <span class="winner-rank-badge badge-bronze">
                                        <i class="bi bi-shield-fill me-1"></i> {{ $bRankLabel }}
                                    </span>
                                    <div class="winner-img-wrap">
                                        <img src="{{ $resolveImg($bPart, 'image_2') }}"
                                            alt="{{ $bPart?->display_name ?: $bPart?->name }}">
                                        <div class="winner-img-overlay d-flex justify-content-between align-items-end">
                                            <!-- <span class="winner-vote-pill text-light">
                                                                                                                                    <i class="bi bi-hand-thumbs-up me-1"></i>
                                                                                                                                    {{ $bPart?->votes_count ? number_format($bPart->votes_count) . ' Votes' : '2nd Runner-Up' }}
                                                                                                                                </span> -->
                                            <!-- <span class="badge bg-danger bg-opacity-75 text-white fw-bold">BRONZE MEDAL</span> -->
                                        </div>
                                    </div>
                                    <div class="p-3 text-center">
                                        <div class="mb-2">
                                            <div class="winner-trophy-ring ring-bronze mx-auto">
                                                <i class="bi {{ $ringIconBronze }}"></i>
                                            </div>
                                        </div>
                                        <h4 class="fw-bold mb-1 text-dark">{{ $bPart?->display_name ?: $bPart?->name }}</h4>
                                        <p
                                            class="text-muted small mb-2 d-flex flex-wrap align-items-center justify-content-center gap-2">
                                            <span><i
                                                    class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $resolveLoc($bPart) }}</span>
                                            <x-gmap-button :participant="$bPart" :location="$resolveLoc($bPart)" />
                                        </p>
                                        @if($bPart?->puja_theme)
                                            <div class="p-2 rounded bg-light text-muted small fw-medium">
                                                <i class="bi bi-check-circle-fill text-secondary me-1"></i>
                                                {{ $bPart?->puja_theme ? 'Theme: ' . $bPart->puja_theme : ' ' }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @foreach ($remainingWinners as $remWinner)
                            @php
                                $rPart = $remWinner->participant;
                                $rRankVal = $getRankValue($remWinner);
                                $hasExplicitRank = !empty($rRankVal);
                                $rRankLabel = $remWinner->rank_order instanceof \App\Enums\WinnerRank
                                    ? $remWinner->rank_order->label()
                                    : ($remWinner->rank_order ?: ' ');
                            @endphp
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="winner-podium-card {{ $hasExplicitRank ? 'winner-bronze' : 'winner-gold' }} h-100">
                                    <span class="winner-rank-badge {{ $hasExplicitRank ? 'badge-bronze' : 'badge-gold' }}">
                                        <i class="bi {{ $hasExplicitRank ? 'bi-star-fill' : 'bi-trophy-fill' }} me-1"></i>
                                        {{ $rRankLabel }}
                                    </span>
                                    <div class="winner-img-wrap">
                                        <img src="{{ $resolveImg($rPart) }}" alt="{{ $rPart?->display_name ?: $rPart?->name }}">
                                        <div class="winner-img-overlay d-flex justify-content-between align-items-end">
                                            <span class="winner-vote-pill text-light">
                                                <i class="bi bi-hand-thumbs-up me-1"></i>
                                                {{ $rPart?->votes_count ? number_format($rPart->votes_count) . ' Votes' : 'Winner' }}
                                            </span>
                                            <span
                                                class="badge {{ $hasExplicitRank ? 'bg-danger bg-opacity-75' : 'bg-warning text-dark' }} fw-bold">
                                                {{ $hasExplicitRank ? 'HONORABLE' : 'WINNER' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-3 text-center">
                                        <div class="mb-2">
                                            <div class="winner-trophy-ring ring-bronze mx-auto">
                                                <i class="bi bi-star"></i>
                                            </div>
                                        </div>
                                        <h4 class="fw-bold mb-1 text-dark">{{ $rPart?->display_name ?: $rPart?->name }}</h4>
                                        <p
                                            class="text-muted small mb-2 d-flex flex-wrap align-items-center justify-content-center gap-2">
                                            <span><i
                                                    class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $resolveLoc($rPart) }}</span>
                                            <x-gmap-button :participant="$rPart" :location="$resolveLoc($rPart)" />
                                        </p>
                                        <div class="p-2 rounded bg-light text-muted small fw-medium">

                                            {{ $rPart?->puja_theme ? 'Theme: ' . $rPart->puja_theme : ($award->description ?: '') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

        </div>
    </section>
@endif