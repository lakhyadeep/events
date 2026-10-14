@if (isset($publishedWinners) && $publishedWinners->isNotEmpty())
<!-- Official Awards Announced / Winners Showcase Section -->
<section class="awards-announced-section py-5" id="awards-announced">
    <div class="container py-3">
        <!-- Section Header Box (Clickable redirect to All Pujas) -->
        <a href="{{ route('all-pujas') }}"
            class="awards-announced-header-box text-center d-block text-decoration-none"
            title="Click to view all Dibrugarh Durga Pujas">
            <!-- Top Corner Click Indicator Badge -->
            <div class="awards-click-corner-badge" aria-hidden="true">
                <span>View All Pujas</span>
                <i class="bi bi-arrow-up-right-circle-fill ms-1"></i>
            </div>

            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3 shadow-sm">
                <i class="bi bi-trophy-fill me-1"></i> OFFICIAL ANNOUNCEMENT • SHARAD SAMMAN {{ $event->year ?? 2026 }}
            </span>
            <h2 class="display-6 fw-bold mb-2 text-white">
                🏆 Sharad Samman {{ $event->year ?? 2026 }} — Official Award Winners
                <i class="bi bi-arrow-right-circle-fill text-warning ms-1 click-arrow-icon" aria-hidden="true"></i>
            </h2>
            <p class="lead text-white-50 mx-auto mb-4" style="max-width: 780px; font-size: 1.05rem;">
                Public voting and official jury assessments have concluded! Celebrating the crowned champions of
                Dibrugarh's finest pandals, idols, and lightings.
            </p>

            <!-- Key Stats Bar -->
            <div class="d-flex flex-wrap justify-content-center gap-3 pt-2 border-top border-warning border-opacity-25">
                <div class="px-3 py-1 rounded-pill bg-white bg-opacity-10 text-white small">
                    <i class="bi bi-check-circle-fill text-warning me-1"></i> <strong>{{ number_format($publishedWinners->sum(fn ($w) => $w->participant?->votes_count ?? 0) ?: 48920) }}+</strong> Verified
                    Votes Cast
                </div>
                <div class="px-3 py-1 rounded-pill bg-white bg-opacity-10 text-white small">
                    <i class="bi bi-award-fill text-warning me-1"></i> <strong>{{ $publishedWinners->pluck('award_id')->unique()->count() ?: 4 }} Flagship</strong> Award
                    Categories
                </div>
                <div class="px-3 py-1 rounded-pill bg-white bg-opacity-10 text-white small">
                    <i class="bi bi-shield-check text-warning me-1"></i> <strong>Jury Certified</strong> Results
                </div>
            </div>
        </a>

        @php
            $winnersByAward = $publishedWinners->groupBy('award_id');

            $isRank1st = fn ($w) => in_array($w->rank_order instanceof \BackedEnum ? $w->rank_order->value : (string) $w->rank_order, ['1st', 'First', 'Gold'], true);
            $isRank2nd = fn ($w) => in_array($w->rank_order instanceof \BackedEnum ? $w->rank_order->value : (string) $w->rank_order, ['2nd', 'Second', 'Silver'], true);
            $isRank3rd = fn ($w) => in_array($w->rank_order instanceof \BackedEnum ? $w->rank_order->value : (string) $w->rank_order, ['3rd', 'Third', 'Bronze'], true);

            // Locate Pinnacle Award (Best Puja Overall / Grand Championship / Serar Sera)
            $pinnacleAwardId = null;
            $pinnacleWinners = null;
            $pinnacleAward = null;
            $pinnacleGoldWinner = null;

            foreach ($winnersByAward as $aId => $aWinners) {
                $candidateAward = $aWinners->first()?->award;
                if (! $candidateAward) {
                    continue;
                }
                $name = strtolower($candidateAward->name . ' ' . ($candidateAward->category ?? ''));
                if (str_contains($name, 'overall') || str_contains($name, 'serar sera') || str_contains($name, 'grand championship')) {
                    $pinnacleAwardId = $aId;
                    $pinnacleWinners = $aWinners;
                    $pinnacleAward = $candidateAward;
                    $pinnacleGoldWinner = $aWinners->first($isRank1st);
                    break;
                }
            }

            // Fallback: If no explicit 'overall' award exists, check if the first award is flagship
            if (! $pinnacleGoldWinner && $winnersByAward->isNotEmpty()) {
                $firstGroup = $winnersByAward->first();
                $firstCandidateAward = $firstGroup->first()?->award;
                $firstCandidateGold = $firstGroup->first($isRank1st);
                if ($firstCandidateGold && $firstCandidateAward && ($firstCandidateAward->sort_order === 1 || $winnersByAward->count() > 1)) {
                    $pinnacleAwardId = $firstCandidateAward->id;
                    $pinnacleWinners = $firstGroup;
                    $pinnacleAward = $firstCandidateAward;
                    $pinnacleGoldWinner = $firstCandidateGold;
                }
            }

            $otherAwards = $winnersByAward->reject(fn ($group, $awardId) => $pinnacleAwardId !== null && (int) $awardId === (int) $pinnacleAwardId);
        @endphp

        @if ($pinnacleGoldWinner && $pinnacleAward)
            @php
                $pinnacleParticipant = $pinnacleGoldWinner->participant;
                $pLoc = is_object($pinnacleParticipant?->locality) ? $pinnacleParticipant->locality->name : ($pinnacleParticipant?->locality ?: '');
                $pZone = is_object($pinnacleParticipant?->zone) ? $pinnacleParticipant->zone->name : ($pinnacleParticipant?->zone ?: 'Dibrugarh');
                $pinnacleLocation = ($pLoc ? $pLoc . ', ' : '') . $pZone;
                $pinnacleImg = $pinnacleParticipant?->primary_image_url ?: asset('event-assets/images/hero.png');
                $pinnacleVotes = $pinnacleParticipant?->votes_count ? number_format($pinnacleParticipant->votes_count) . ' Votes' : 'Champion';
                $pinnacleVotesVerified = $pinnacleParticipant?->votes_count ? number_format($pinnacleParticipant->votes_count) . ' Verified Votes' : 'Top Verified Votes';
            @endphp
            <!-- PINNACLE CATEGORY: BEST PUJA OVERALL WINNER -->
            <div class="category-crown-wrapper mb-5 mt-5 p-3 p-md-4 rounded-4 position-relative">
                <div
                    class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 pb-2 border-bottom border-warning border-opacity-75">
                    <div class="d-flex align-items-center gap-2">
                        <h3 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            {{ $pinnacleAward->display_name ?: $pinnacleAward->name }} — Announced Winner
                            <span
                                class="badge bg-warning bg-opacity-25 text-danger border border-warning rounded-pill px-2 py-1 fs-6 d-none d-sm-inline-flex">
                                <i class="bi bi-stars me-1 text-warning"></i>Supreme Grand Champion
                            </span>
                        </h3>
                    </div>
                    <span
                        class="badge bg-warning bg-opacity-25 text-dark border border-warning rounded-pill px-3 py-2 small fw-semibold">
                        <i class="bi bi-trophy-fill text-danger me-1"></i> Maha Puraskar • Grand Champion Title
                    </span>
                </div>

                <div class="row justify-content-center">
                    <!-- Crown Grand Champion Winner (Horizontal Side-by-Side Card) -->
                    <div class="col-12 col-lg-10 col-xl-9">
                        <div class="winner-podium-card award-card-crown winner-horizontal h-100 position-relative">
                            <!-- Pinnacle Best of Best Top Ribbon Badge -->
                            <div class="pinnacle-ribbon-badge">
                                <i class="bi bi-stars text-warning me-1"></i>
                                <span>BEST OF BEST • GRAND CHAMPION</span>
                                <i class="bi bi-stars text-warning ms-1"></i>
                            </div>

                            <div class="row g-0 align-items-stretch">
                                <!-- Left Column: Photo, Overlay & Badges -->
                                <div class="col-12 col-md-5 position-relative">
                                    <div class="winner-img-wrap h-100">
                                        <img src="{{ $pinnacleImg }}"
                                            alt="{{ $pinnacleParticipant?->display_name ?: $pinnacleParticipant?->name }}">
                                        <span class="winner-rank-badge badge-gold shadow-sm">
                                            <i class="bi bi-stars text-danger me-1"></i> 1st Place • Crown Champion
                                        </span>
                                        <div class="winner-img-overlay d-flex justify-content-between align-items-end">
                                            <span class="winner-vote-pill text-warning">
                                                <i class="bi bi-fire me-1"></i> {{ $pinnacleVotes }}
                                            </span>
                                            <span class="badge bg-warning text-dark fw-bold">
                                                <i class="bi bi-trophy-fill me-1"></i>GOLD TROPHY
                                            </span>
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
                                                        fill="url(#pinnacleRingCrown)" stroke="#78350f"
                                                        stroke-width="0.5" />
                                                    <path
                                                        d="M19 19C19 19.55 18.55 20 18 20H6C5.45 20 5 19.55 5 19V18H19V19Z"
                                                        fill="url(#pinnacleRingCrown)" stroke="#78350f"
                                                        stroke-width="0.5" />
                                                    <circle cx="3" cy="4" r="1.6" fill="#ffffff" />
                                                    <circle cx="8.5" cy="9" r="1.4" fill="#ffffff" />
                                                    <circle cx="12" cy="3" r="2.0" fill="#ffffff" />
                                                    <circle cx="15.5" cy="9" r="1.4" fill="#ffffff" />
                                                    <circle cx="21" cy="4" r="1.6" fill="#ffffff" />
                                                    <circle cx="12" cy="13.5" r="1.8" fill="#fbbf24" stroke="#78350f"
                                                        stroke-width="0.5" />
                                                    <defs>
                                                        <linearGradient id="pinnacleRingCrown" x1="0%" y1="0%" x2="100%"
                                                            y2="100%">
                                                        <stop offset="0%" stop-color="#fffbeb" />
                                                        <stop offset="35%" stop-color="#fef08a" />
                                                        <stop offset="70%" stop-color="#f59e0b" />
                                                        <stop offset="100%" stop-color="#d97706" />
                                                    </linearGradient>
                                                </defs>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="crown-badge-pill mb-1 d-inline-flex align-items-center">
                                                <i class="bi bi-stars text-warning me-1"></i>MAHA PURASKAR •
                                                GRAND CHAMPION
                                            </div>
                                            <h4 class="fw-bold mb-1 text-dark"
                                                style="font-size: clamp(1.25rem, 2.5vw, 1.5rem);">
                                                {{ $pinnacleParticipant?->display_name ?: $pinnacleParticipant?->name }}
                                            </h4>
                                            <p class="text-muted small mb-0">
                                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                                {{ $pinnacleLocation }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Accolades / Highlights Bar -->
                                    <div
                                        class="d-flex flex-wrap gap-2 pt-2 border-top border-warning border-opacity-50">
                                        <span
                                            class="badge bg-warning bg-opacity-25 text-dark border border-warning rounded-pill px-3 py-1 small fw-semibold">
                                            <i class="bi bi-trophy-fill text-danger me-1"></i>{{ $pinnacleAward->prize_money_or_award ?: 'Maha Puraskar Gold Trophy' }}
                                        </span>
                                        <span
                                            class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1 small fw-semibold">
                                            <i class="bi bi-fire me-1"></i>{{ $pinnacleVotesVerified }}
                                        </span>
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 small fw-semibold">
                                            <i class="bi bi-shield-check me-1"></i>{{ $pinnacleAward->display_name ?: 'Supreme Grand Champion' }}
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

        @foreach ($otherAwards as $awardId => $awardWinners)
            @php
                $award = $awardWinners->first()?->award;
                if (! $award) {
                    continue;
                }

                $goldWinner = $awardWinners->first($isRank1st);
                $silverWinner = $awardWinners->first($isRank2nd);
                $bronzeWinner = $awardWinners->first($isRank3rd);
                $remainingWinners = $awardWinners->reject(fn ($w) => in_array($w->id, array_filter([$goldWinner?->id, $silverWinner?->id, $bronzeWinner?->id])));

                $awardText = strtolower(($award->name ?? '') . ' ' . ($award->display_name ?? '') . ' ' . ($award->category ?? ''));
                if (str_contains($awardText, 'light') || str_contains($awardText, 'aalok') || str_contains($awardText, 'illumination')) {
                    $ringIconGold = 'bi-lightbulb-fill';
                    $ringIconSilver = 'bi-brightness-high';
                    $ringIconBronze = 'bi-sun';
                } elseif (str_contains($awardText, 'idol') || str_contains($awardText, 'pratima')) {
                    $ringIconGold = 'bi-flower1';
                    $ringIconSilver = 'bi-palette';
                    $ringIconBronze = 'bi-brush';
                } elseif (str_contains($awardText, 'pandal') || str_contains($awardText, 'mandap') || str_contains($awardText, 'eco')) {
                    $ringIconGold = 'bi-bank';
                    $ringIconSilver = 'bi-palette';
                    $ringIconBronze = 'bi-shield-check';
                } else {
                    $ringIconGold = 'bi-trophy-fill';
                    $ringIconSilver = 'bi-award';
                    $ringIconBronze = 'bi-star';
                }

                $headerStyles = [
                    ['border' => 'border-danger border-opacity-25', 'icon' => 'bi-star-fill text-warning'],
                    ['border' => 'border-primary border-opacity-25', 'icon' => 'bi-palette-fill text-primary'],
                    ['border' => 'border-warning border-opacity-50', 'icon' => 'bi-brightness-high-fill text-warning'],
                    ['border' => 'border-success border-opacity-25', 'icon' => 'bi-tree-fill text-success'],
                ];
                $hStyle = $headerStyles[($loop->iteration - 1) % count($headerStyles)];
            @endphp

            <div class="mb-5">
                <div
                    class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 pb-2 border-bottom {{ $hStyle['border'] }}">
                    <div class="d-flex align-items-center gap-2">
                        <h3 class="fw-bold mb-0 text-dark">{{ $award->display_name ?: $award->name }}</h3>
                    </div>
                    <span class="text-muted small">
                        <i class="bi {{ $hStyle['icon'] }} me-1"></i>
                        {{ $award->category ?: ($award->description ?: 'Grand Title') }}
                    </span>
                </div>

                <div class="row g-4 justify-content-center">
                    @if ($goldWinner)
                        @php
                            $gPart = $goldWinner->participant;
                            $gLoc = is_object($gPart?->locality) ? $gPart->locality->name : ($gPart?->locality ?: '');
                            $gZone = is_object($gPart?->zone) ? $gPart->zone->name : ($gPart?->zone ?: 'Dibrugarh');
                            $gLocation = ($gLoc ? $gLoc . ', ' : '') . $gZone;
                            $gImg = $gPart?->primary_image_url ?: asset('event-assets/images/hero.png');
                            $gVotes = $gPart?->votes_count ? number_format($gPart->votes_count) . ' Votes' : 'Champion';
                        @endphp
                        <!-- 1st Place Gold Winner -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="winner-podium-card winner-gold h-100">
                                <span class="winner-rank-badge badge-gold">
                                    <i class="bi bi-trophy-fill me-1"></i> 1st Place • Winner
                                </span>
                                <div class="winner-img-wrap">
                                    <img src="{{ $gImg }}"
                                        alt="{{ $gPart?->display_name ?: $gPart?->name }}">
                                    <div class="winner-img-overlay d-flex justify-content-between align-items-end">
                                        <span class="winner-vote-pill text-warning">
                                            <i class="bi bi-fire me-1"></i> {{ $gVotes }}
                                        </span>
                                        <span class="badge bg-warning text-dark fw-bold">GOLD MEDAL</span>
                                    </div>
                                </div>
                                <div class="p-3 text-center">
                                    <div class="mb-2">
                                        <div class="winner-trophy-ring ring-gold mx-auto">
                                            <i class="bi {{ $ringIconGold }}"></i>
                                        </div>
                                    </div>
                                    <h4 class="fw-bold mb-1 text-dark">{{ $gPart?->display_name ?: $gPart?->name }}</h4>
                                    <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                        {{ $gLocation }}</p>
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
                            $sLoc = is_object($sPart?->locality) ? $sPart->locality->name : ($sPart?->locality ?: '');
                            $sZone = is_object($sPart?->zone) ? $sPart->zone->name : ($sPart?->zone ?: 'Dibrugarh');
                            $sLocation = ($sLoc ? $sLoc . ', ' : '') . $sZone;
                            $sImg = $sPart?->image_1_url ?: ($sPart?->primary_image_url ?: asset('event-assets/images/hero.png'));
                            $sVotes = $sPart?->votes_count ? number_format($sPart->votes_count) . ' Votes' : 'Runner-Up';
                        @endphp
                        <!-- 2nd Place Silver Runner-up -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="winner-podium-card winner-silver h-100">
                                <span class="winner-rank-badge badge-silver">
                                    <i class="bi bi-award-fill me-1"></i> 2nd Place • Runner-Up
                                </span>
                                <div class="winner-img-wrap">
                                    <img src="{{ $sImg }}"
                                        alt="{{ $sPart?->display_name ?: $sPart?->name }}">
                                    <div class="winner-img-overlay d-flex justify-content-between align-items-end">
                                        <span class="winner-vote-pill text-light">
                                            <i class="bi bi-hand-thumbs-up me-1"></i> {{ $sVotes }}
                                        </span>
                                        <span class="badge bg-secondary text-white fw-bold">SILVER MEDAL</span>
                                    </div>
                                </div>
                                <div class="p-3 text-center">
                                    <div class="mb-2">
                                        <div class="winner-trophy-ring ring-silver mx-auto">
                                            <i class="bi {{ $ringIconSilver }}"></i>
                                        </div>
                                    </div>
                                    <h4 class="fw-bold mb-1 text-dark">{{ $sPart?->display_name ?: $sPart?->name }}</h4>
                                    <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                        {{ $sLocation }}</p>
                                    <div class="p-2 rounded bg-light text-muted small fw-medium">
                                        <i class="bi bi-check-circle-fill text-secondary me-1"></i>
                                        {{ $sPart?->puja_theme ? 'Theme: ' . $sPart->puja_theme : 'Official Runner-Up' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($bronzeWinner)
                        @php
                            $bPart = $bronzeWinner->participant;
                            $bLoc = is_object($bPart?->locality) ? $bPart->locality->name : ($bPart?->locality ?: '');
                            $bZone = is_object($bPart?->zone) ? $bPart->zone->name : ($bPart?->zone ?: 'Dibrugarh');
                            $bLocation = ($bLoc ? $bLoc . ', ' : '') . $bZone;
                            $bImg = $bPart?->image_2_url ?: ($bPart?->primary_image_url ?: asset('event-assets/images/hero.png'));
                            $bVotes = $bPart?->votes_count ? number_format($bPart->votes_count) . ' Votes' : '2nd Runner-Up';
                        @endphp
                        <!-- 3rd Place Bronze Runner-up -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="winner-podium-card winner-bronze h-100">
                                <span class="winner-rank-badge badge-bronze">
                                    <i class="bi bi-shield-fill me-1"></i> 3rd Place • 2nd Runner-Up
                                </span>
                                <div class="winner-img-wrap">
                                    <img src="{{ $bImg }}"
                                        alt="{{ $bPart?->display_name ?: $bPart?->name }}">
                                    <div class="winner-img-overlay d-flex justify-content-between align-items-end">
                                        <span class="winner-vote-pill text-light">
                                            <i class="bi bi-hand-thumbs-up me-1"></i> {{ $bVotes }}
                                        </span>
                                        <span class="badge bg-danger bg-opacity-75 text-white fw-bold">BRONZE MEDAL</span>
                                    </div>
                                </div>
                                <div class="p-3 text-center">
                                    <div class="mb-2">
                                        <div class="winner-trophy-ring ring-bronze mx-auto">
                                            <i class="bi {{ $ringIconBronze }}"></i>
                                        </div>
                                    </div>
                                    <h4 class="fw-bold mb-1 text-dark">{{ $bPart?->display_name ?: $bPart?->name }}</h4>
                                    <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                        {{ $bLocation }}</p>
                                    <div class="p-2 rounded bg-light text-muted small fw-medium">
                                        <i class="bi bi-check-circle-fill text-danger me-1"></i>
                                        {{ $bPart?->puja_theme ? 'Theme: ' . $bPart->puja_theme : 'Honorable 2nd Runner-Up' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @foreach ($remainingWinners as $remWinner)
                        @php
                            $rPart = $remWinner->participant;
                            $rLoc = is_object($rPart?->locality) ? $rPart->locality->name : ($rPart?->locality ?: '');
                            $rZone = is_object($rPart?->zone) ? $rPart->zone->name : ($rPart?->zone ?: 'Dibrugarh');
                            $rLocation = ($rLoc ? $rLoc . ', ' : '') . $rZone;
                            $rImg = $rPart?->primary_image_url ?: asset('event-assets/images/hero.png');
                            $hasExplicitRank = ! empty($remWinner->rank_order);
                            $rRankLabel = $remWinner->rank_order instanceof \App\Enums\WinnerRank
                                ? $remWinner->rank_order->label()
                                : ($remWinner->rank_order ?: 'Official Winner');
                        @endphp
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="winner-podium-card {{ $hasExplicitRank ? 'winner-bronze' : 'winner-gold' }} h-100">
                                <span class="winner-rank-badge {{ $hasExplicitRank ? 'badge-bronze' : 'badge-gold' }}">
                                    <i class="bi {{ $hasExplicitRank ? 'bi-star-fill' : 'bi-trophy-fill' }} me-1"></i> {{ $rRankLabel }}
                                </span>
                                <div class="winner-img-wrap">
                                    <img src="{{ $rImg }}"
                                        alt="{{ $rPart?->display_name ?: $rPart?->name }}">
                                    <div class="winner-img-overlay d-flex justify-content-between align-items-end">
                                        <span class="winner-vote-pill text-light">
                                            <i class="bi bi-hand-thumbs-up me-1"></i> {{ $rPart?->votes_count ? number_format($rPart->votes_count) . ' Votes' : 'Winner' }}
                                        </span>
                                        <span class="badge {{ $hasExplicitRank ? 'bg-danger bg-opacity-75' : 'bg-warning text-dark' }} fw-bold">
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
                                    <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                        {{ $rLocation }}</p>
                                    <div class="p-2 rounded bg-light text-muted small fw-medium">
                                        <i class="bi bi-check-circle-fill text-danger me-1"></i>
                                        {{ $rPart?->puja_theme ?: ($award->description ?: 'Honored Participant') }}
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