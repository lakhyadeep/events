<section class="awards-announced-section py-5" id="awards-announced">
    <div class="container py-3">
        <!-- Section Header Box -->
        <div class="awards-announced-header-box text-center mb-5">
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3 shadow-sm">
                <i class="bi bi-trophy-fill me-1"></i> OFFICIAL ANNOUNCEMENT • {{ strtoupper($event->display_name ?? 'SHARAD SAMMAN 2026') }}
            </span>
            <h2 class="display-6 fw-bold mb-2 text-white">🏆 {{ $event->display_name ?? 'Sharad Samman 2026' }} — Official Award Winners</h2>
            <p class="lead text-white-50 mx-auto mb-4" style="max-width: 780px; font-size: 1.05rem;">
                Public voting and official jury assessments have concluded! Celebrating the crowned champions of Dibrugarh's finest pandals, idols, and lightings.
            </p>

            <!-- Key Stats Bar -->
            <div class="d-flex flex-wrap justify-content-center gap-3 pt-2 border-top border-warning border-opacity-25">
                <div class="px-3 py-1 rounded-pill bg-white bg-opacity-10 text-white small">
                    <i class="bi bi-check-circle-fill text-warning me-1"></i> <strong>48,920+</strong> Verified Votes Cast
                </div>
                <div class="px-3 py-1 rounded-pill bg-white bg-opacity-10 text-white small">
                    <i class="bi bi-award-fill text-warning me-1"></i> <strong>{{ $event->awards->count() ?: 3 }} Flagship</strong> Award Categories
                </div>
                <div class="px-3 py-1 rounded-pill bg-white bg-opacity-10 text-white small">
                    <i class="bi bi-shield-check text-warning me-1"></i> <strong>Jury Certified</strong> Results
                </div>
            </div>
        </div>

        @php
            $winnersGrouped = (isset($publishedWinners) && $publishedWinners->isNotEmpty())
                ? $publishedWinners->groupBy('award_id')
                : (isset($event->winners) && $event->winners->isNotEmpty() ? $event->winners->groupBy('award_id') : collect());
        @endphp

        @if ($winnersGrouped->isNotEmpty())
            @foreach ($winnersGrouped as $awardId => $awardWinners)
                @php
                    $award = $awardWinners->first()->award ?? $event->awards->firstWhere('id', $awardId);
                    $goldWinner = $awardWinners->first(fn($w) => ($w->rank_order?->value ?? $w->rank_order) === '1st');
                    $silverWinner = $awardWinners->first(fn($w) => ($w->rank_order?->value ?? $w->rank_order) === '2nd');
                    $bronzeWinner = $awardWinners->first(fn($w) => ($w->rank_order?->value ?? $w->rank_order) === '3rd');
                @endphp
                <div class="mb-5">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 pb-2 border-bottom border-danger border-opacity-25">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-danger text-white rounded-pill px-3 py-2 fs-6">
                                <i class="bi bi-trophy-fill me-1"></i> CATEGORY {{ sprintf('%02d', $loop->iteration) }}
                            </span>
                            <h3 class="fw-bold mb-0 text-dark">{{ $award?->display_name ?: ($award?->name ?? 'Category ' . $loop->iteration) }} — Announced Winners</h3>
                        </div>
                        @if ($award?->category)
                            <span class="text-muted small"><i class="bi bi-star-fill text-warning me-1"></i> {{ $award->category }}</span>
                        @endif
                    </div>

                    <div class="row g-4 justify-content-center">
                        @if ($goldWinner)
                            <!-- 1st Place Gold Winner -->
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="winner-podium-card winner-gold h-100">
                                    <span class="winner-rank-badge badge-gold">
                                        <i class="bi bi-crown-fill me-1"></i> 1st Place • Winner
                                    </span>
                                    <div class="winner-img-wrap">
                                        <img src="{{ $goldWinner->participant?->primary_image_url ?: ($goldWinner->participant?->image_1_url ?: 'https://images.unsplash.com/photo-1728921957375-877340feaf6a?q=80&w=2070&auto=format&fit=crop') }}"
                                            alt="{{ $goldWinner->participant?->display_name ?: $goldWinner->participant?->name }}">
                                        <div class="winner-img-overlay d-flex justify-content-between align-items-end">
                                            <span class="winner-vote-pill text-warning">
                                                <i class="bi bi-fire me-1"></i> Certified Champion
                                            </span>
                                            <span class="badge bg-warning text-dark fw-bold">GOLD MEDAL</span>
                                        </div>
                                    </div>
                                    <div class="p-3 text-center">
                                        <div class="mb-2">
                                            <div class="winner-trophy-ring ring-gold mx-auto">
                                                <i class="bi bi-trophy-fill"></i>
                                            </div>
                                        </div>
                                        <h4 class="fw-bold mb-1 text-dark">{{ $goldWinner->participant?->display_name ?: $goldWinner->participant?->name }}</h4>
                                        <p class="text-muted small mb-2">
                                            <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                            {{ $goldWinner->participant?->locality ? $goldWinner->participant?->locality . ', ' : '' }}{{ $goldWinner->participant?->zone ?: 'Dibrugarh' }}
                                        </p>
                                        <div class="p-2 rounded bg-warning bg-opacity-10 text-dark small fw-medium">
                                            <i class="bi bi-award-fill text-warning me-1"></i>
                                            {{ $goldWinner->participant?->puja_theme ? 'Theme: ' . $goldWinner->participant?->puja_theme : 'Winner of ' . ($award?->name ?? 'Grand Championship') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($silverWinner)
                            <!-- 2nd Place Silver Runner-up -->
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="winner-podium-card winner-silver h-100">
                                    <span class="winner-rank-badge badge-silver">
                                        <i class="bi bi-award-fill me-1"></i> 2nd Place • Runner-Up
                                    </span>
                                    <div class="winner-img-wrap">
                                        <img src="{{ $silverWinner->participant?->image_1_url ?: ($silverWinner->participant?->primary_image_url ?: 'https://images.unsplash.com/photo-1728493118702-febd0b9a0fab?q=80&w=1170&auto=format&fit=crop') }}"
                                            alt="{{ $silverWinner->participant?->display_name ?: $silverWinner->participant?->name }}">
                                        <div class="winner-img-overlay d-flex justify-content-between align-items-end">
                                            <span class="winner-vote-pill text-light">
                                                <i class="bi bi-hand-thumbs-up me-1"></i> Official Runner-Up
                                            </span>
                                            <span class="badge bg-secondary text-white fw-bold">SILVER MEDAL</span>
                                        </div>
                                    </div>
                                    <div class="p-3 text-center">
                                        <div class="mb-2">
                                            <div class="winner-trophy-ring ring-silver mx-auto">
                                                <i class="bi bi-award"></i>
                                            </div>
                                        </div>
                                        <h4 class="fw-bold mb-1 text-dark">{{ $silverWinner->participant?->display_name ?: $silverWinner->participant?->name }}</h4>
                                        <p class="text-muted small mb-2">
                                            <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                            {{ $silverWinner->participant?->locality ? $silverWinner->participant?->locality . ', ' : '' }}{{ $silverWinner->participant?->zone ?: 'Dibrugarh' }}
                                        </p>
                                        <div class="p-2 rounded bg-light text-muted small fw-medium">
                                            <i class="bi bi-check-circle-fill text-secondary me-1"></i>
                                            {{ $silverWinner->participant?->puja_theme ? 'Theme: ' . $silverWinner->participant?->puja_theme : 'Runner-Up for Outstanding Presentation' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($bronzeWinner)
                            <!-- 3rd Place Bronze Runner-up -->
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="winner-podium-card winner-bronze h-100">
                                    <span class="winner-rank-badge badge-bronze">
                                        <i class="bi bi-shield-fill me-1"></i> 3rd Place • 2nd Runner-Up
                                    </span>
                                    <div class="winner-img-wrap">
                                        <img src="{{ $bronzeWinner->participant?->image_2_url ?: ($bronzeWinner->participant?->primary_image_url ?: 'https://images.unsplash.com/photo-1756399220348-7964d338cb19?q=80&w=1074&auto=format&fit=crop') }}"
                                            alt="{{ $bronzeWinner->participant?->display_name ?: $bronzeWinner->participant?->name }}">
                                        <div class="winner-img-overlay d-flex justify-content-between align-items-end">
                                            <span class="winner-vote-pill text-light">
                                                <i class="bi bi-star-fill me-1"></i> 2nd Runner-Up
                                            </span>
                                            <span class="badge bg-danger bg-opacity-75 text-white fw-bold">BRONZE MEDAL</span>
                                        </div>
                                    </div>
                                    <div class="p-3 text-center">
                                        <div class="mb-2">
                                            <div class="winner-trophy-ring ring-bronze mx-auto">
                                                <i class="bi bi-star"></i>
                                            </div>
                                        </div>
                                        <h4 class="fw-bold mb-1 text-dark">{{ $bronzeWinner->participant?->display_name ?: $bronzeWinner->participant?->name }}</h4>
                                        <p class="text-muted small mb-2">
                                            <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                            {{ $bronzeWinner->participant?->locality ? $bronzeWinner->participant?->locality . ', ' : '' }}{{ $bronzeWinner->participant?->zone ?: 'Dibrugarh' }}
                                        </p>
                                        <div class="p-2 rounded bg-light text-muted small fw-medium">
                                            <i class="bi bi-check-circle-fill text-danger me-1"></i>
                                            {{ $bronzeWinner->participant?->puja_theme ? 'Theme: ' . $bronzeWinner->participant?->puja_theme : 'Honorable 2nd Runner-Up' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <!-- Fallback Category 1: Best Puja -->
            <div class="mb-5">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 pb-2 border-bottom border-danger border-opacity-25">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger text-white rounded-pill px-3 py-2 fs-6">
                            <i class="bi bi-trophy-fill me-1"></i> CATEGORY 01
                        </span>
                        <h3 class="fw-bold mb-0 text-dark">Best Puja — Announced Winners</h3>
                    </div>
                    <span class="text-muted small"><i class="bi bi-star-fill text-warning me-1"></i> Grand Flagship Title</span>
                </div>

                <div class="row g-4 justify-content-center">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="winner-podium-card winner-gold h-100">
                            <span class="winner-rank-badge badge-gold">
                                <i class="bi bi-crown-fill me-1"></i> 1st Place • Winner
                            </span>
                            <div class="winner-img-wrap">
                                <img src="https://images.unsplash.com/photo-1728921957375-877340feaf6a?q=80&w=2070&auto=format&fit=crop" alt="Winner">
                                <div class="winner-img-overlay d-flex justify-content-between align-items-end">
                                    <span class="winner-vote-pill text-warning"><i class="bi bi-fire me-1"></i> 5,537 Votes</span>
                                    <span class="badge bg-warning text-dark fw-bold">GOLD MEDAL</span>
                                </div>
                            </div>
                            <div class="p-3 text-center">
                                <div class="mb-2">
                                    <div class="winner-trophy-ring ring-gold mx-auto"><i class="bi bi-trophy-fill"></i></div>
                                </div>
                                <h4 class="fw-bold mb-1 text-dark">New Market Durga Puja</h4>
                                <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i> New Market Road, Dibrugarh</p>
                                <div class="p-2 rounded bg-warning bg-opacity-10 text-dark small fw-medium">
                                    Winner of Best Puja 2026 for Exceptional Architecture & Theme Excellence.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</section>