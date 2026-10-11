@php
    $contestants = isset($participants) && $participants->isNotEmpty() ? $participants : collect();
    $eventAwards = (isset($event) && $event->awards) ? $event->awards->where('is_active', true) : collect();
    $pujaAward = $eventAwards->first(fn($a) => str_contains(strtolower($a->name), 'puja') && !str_contains(strtolower($a->name), 'people')) ?? $eventAwards->first();
    $pratimaAward = $eventAwards->first(fn($a) => str_contains(strtolower($a->name), 'pratima') || str_contains(strtolower($a->name), 'idol')) ?? $eventAwards->skip(1)->first();
    $lightingAward = $eventAwards->first(fn($a) => str_contains(strtolower($a->name), 'light') || str_contains(strtolower($a->name), 'illumination')) ?? $eventAwards->skip(2)->first();
@endphp

@if ($contestants->isNotEmpty())
<div class="container">
    <div class="d-flex flex-column align-items-center mb-4 text-center pujas-header-box">
        <span class="pujas-badge-tag mb-2">
            <i class="bi bi-trophy-fill text-warning me-1"></i> SHARAD SAMMAN VOTING HUB {{ $event->year ?? 2026 }}
        </span>
        <h2 class="fw-bold mb-2 pujas-header-title">Vote for the Best — {{ $event->display_name ?? 'Sharad Samman 2026' }}</h2>
        <p class="pujas-header-subtitle mb-3">Explore the shortlisted entries and cast your vote in each category</p>
        <div class="d-flex flex-wrap justify-content-center gap-2 mt-1">
            <a href="#cat-best-puja"
                class="btn btn-sm btn-danger rounded-pill px-3 py-2 fw-semibold shadow-sm pujas-nav-btn">
                <i class="bi bi-trophy-fill me-1 text-warning"></i> 1. Best Puja
            </a>
            <a href="#cat-best-pratima"
                class="btn btn-sm btn-outline-light rounded-pill px-3 py-2 fw-semibold pujas-nav-btn">
                <i class="bi bi-flower1 me-1 text-info"></i> 2. Best Pratima
            </a>
            <a href="#cat-best-lighting"
                class="btn btn-sm btn-outline-warning text-warning rounded-pill px-3 py-2 fw-semibold pujas-nav-btn"
                style="border-color: #f59e0b;">
                <i class="bi bi-lightbulb-fill me-1 text-warning"></i> 3. Best Lighting
            </a>
        </div>
    </div>

    <!-- Award Details Modal -->
    <div class="modal fade" id="awardDetailModal" tabindex="-1" aria-labelledby="awardDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <div class="custom-modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold" id="awardDetailModalLabel">
                        <i class="bi bi-award-fill me-2 text-warning"></i>
                        <span id="modalCategoryTitle">Best Puja</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-white text-center">
                    <div id="modalMedalPreview" class="mb-3 d-inline-block"></div>
                    <h4 class="fw-bold mb-1" id="modalRankText" style="color: #7c0b15;">Winner TBA</h4>
                    <p class="text-muted small mb-3" id="modalStatusDesc">The official winner for this category
                        will be announced live following jury assessments and public voting.
                    </p>

                    <div class="text-start p-3 rounded-3"
                        style="background-color: #fdfaf5; border: 1px dashed #dfc6aa;">
                        <div class="fw-bold small text-uppercase mb-2 text-secondary">Key Judging Criteria:</div>
                        <div id="modalCriteriaTags">
                            <span class="criteria-tag">Theme & Creativity</span>
                            <span class="criteria-tag">Eco-friendly Decor</span>
                            <span class="criteria-tag">Crowd Safety & Discipline</span>
                            <span class="criteria-tag">Public Voting Score</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center bg-light border-0 py-3">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill"
                        data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn text-white px-4 rounded-pill"
                        style="background-color: #a5131d;" id="modalVoteActionBtn">
                        <i class="bi bi-hand-thumbs-up-fill me-1"></i> Cast Your Vote
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Embedded Awards Box -->

    <!-- CATEGORY 1: BEST PUJA -->
    <div class="category-section-block cat-puja-theme">
        <div class="category-title-wrapper" id="cat-best-puja">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="category-icon-box">
                        <i class="bi bi-trophy-fill"></i>
                    </div>
                    <div>
                        <h3 class="category-title-text">Best Puja</h3>
                        <div class="category-subtitle-text">Nominated Pandals • {{ $event->display_name ?? 'Sharad Samman 2026' }}</div>
                    </div>
                </div>
                <span class="category-badge-pill">
                    <i class="bi bi-fire text-danger me-1"></i> Flagship Category
                </span>
            </div>
        </div>

        <div class="row g-4">
            @foreach ($contestants as $p)
                <div class="col-md-4">
                    <div class="vote-card h-100 p-3">
                        <div class="position-relative mb-3">
                            <img src="{{ $p->primary_image_url ?: ($p->image_1_url ?: 'https://images.unsplash.com/photo-1728921957375-877340feaf6a?q=80&w=2070&auto=format&fit=crop') }}"
                                alt="{{ $p->display_name ?: $p->name }}" class="img-fluid rounded-3 w-100"
                                style="height: 200px; object-fit: cover;">
                        </div>
                        <h5 class="fw-bold mb-1">{{ $p->display_name ?: $p->name }}</h5>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                            {{ $p->locality ? $p->locality . ', ' : '' }}{{ $p->zone ?: 'Dibrugarh' }}
                        </p>
                        @if (!empty($p->puja_theme))
                            <p class="small text-secondary mb-3 fst-italic" style="font-size: 0.8rem;">
                                <i class="bi bi-palette me-1 text-warning"></i> Theme: {{ $p->puja_theme }}
                            </p>
                        @endif
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-auto">
                            <div>
                                <small class="text-muted d-block">Zone</small>
                                <span class="fw-bold fs-6 text-danger">{{ $p->zone ?: 'Central' }}</span>
                            </div>
                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3 vote-trigger-btn"
                                data-pandal="{{ $p->name }}"
                                data-participant-id="{{ $p->id }}"
                                data-award-id="{{ $pujaAward?->id ?? '' }}">
                                <i class="bi bi-hand-thumbs-up me-1"></i> Vote
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- CATEGORY 2: BEST PRATIMA -->
    <div class="category-section-block cat-pratima-theme">
        <div class="category-title-wrapper" id="cat-best-pratima">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="category-icon-box"
                        style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                        <i class="bi bi-flower1 text-warning"></i>
                    </div>
                    <div>
                        <h3 class="category-title-text">Best Pratima</h3>
                        <div class="category-subtitle-text">Exquisite Clay Idol Artistry & Traditional Craftsmanship</div>
                    </div>
                </div>
                <span class="category-badge-pill"
                    style="background: rgba(30, 41, 59, 0.08); color: #1e293b; border-color: rgba(30, 41, 59, 0.2);">
                    <i class="bi bi-palette-fill text-primary me-1"></i> Traditional Art
                </span>
            </div>
        </div>

        <div class="row g-4">
            @foreach ($contestants as $p)
                <div class="col-md-4">
                    <div class="vote-card h-100 p-3">
                        <div class="position-relative mb-3">
                            <img src="{{ $p->image_1_url ?: ($p->primary_image_url ?: 'https://images.unsplash.com/photo-1756399220348-7964d338cb19?q=80&w=1074&auto=format&fit=crop') }}"
                                alt="{{ $p->display_name ?: $p->name }}" class="img-fluid rounded-3 w-100"
                                style="height: 200px; object-fit: cover;">
                        </div>
                        <h5 class="fw-bold mb-1">{{ $p->display_name ?: $p->name }}</h5>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                            {{ $p->locality ? $p->locality . ', ' : '' }}{{ $p->zone ?: 'Dibrugarh' }}
                        </p>
                        @if (!empty($p->idol_artist))
                            <p class="small text-secondary mb-3" style="font-size: 0.8rem;">
                                <i class="bi bi-brush me-1 text-primary"></i> Sculptor: {{ $p->idol_artist }}
                            </p>
                        @endif
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-auto">
                            <div>
                                <small class="text-muted d-block">Zone</small>
                                <span class="fw-bold fs-6 text-dark">{{ $p->zone ?: 'Central' }}</span>
                            </div>
                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3 vote-trigger-btn"
                                data-pandal="{{ $p->name }}"
                                data-participant-id="{{ $p->id }}"
                                data-award-id="{{ $pratimaAward?->id ?? '' }}">
                                <i class="bi bi-hand-thumbs-up me-1"></i> Vote
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- CATEGORY 3: BEST LIGHTING -->
    <div class="category-section-block cat-lighting-theme">
        <div class="category-title-wrapper" id="cat-best-lighting">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="category-icon-box"
                        style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%);">
                        <i class="bi bi-lightbulb-fill text-white"></i>
                    </div>
                    <div>
                        <h3 class="category-title-text">Best Lighting</h3>
                        <div class="category-subtitle-text">Grand Illuminations & Light Gate Displays</div>
                    </div>
                </div>
                <span class="category-badge-pill"
                    style="background: rgba(217, 119, 6, 0.1); color: #b45309; border-color: rgba(217, 119, 6, 0.25);">
                    <i class="bi bi-brightness-high-fill text-warning me-1"></i> Illumination Award
                </span>
            </div>
        </div>

        <div class="row g-4">
            @foreach ($contestants as $p)
                <div class="col-md-4">
                    <div class="vote-card h-100 p-3">
                        <div class="position-relative mb-3">
                            <img src="{{ $p->image_2_url ?: ($p->primary_image_url ?: 'https://images.unsplash.com/photo-1541701494587-cb58502866ab?auto=format&fit=crop&w=800&q=80') }}"
                                alt="{{ $p->display_name ?: $p->name }}" class="img-fluid rounded-3 w-100"
                                style="height: 200px; object-fit: cover;">
                        </div>
                        <h5 class="fw-bold mb-1">{{ $p->display_name ?: $p->name }}</h5>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                            {{ $p->locality ? $p->locality . ', ' : '' }}{{ $p->zone ?: 'Dibrugarh' }}
                        </p>
                        @if (!empty($p->light_designer))
                            <p class="small text-secondary mb-3" style="font-size: 0.8rem;">
                                <i class="bi bi-lightbulb me-1 text-warning"></i> Illumination: {{ $p->light_designer }}
                            </p>
                        @endif
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-auto">
                            <div>
                                <small class="text-muted d-block">Zone</small>
                                <span class="fw-bold fs-6 text-warning">{{ $p->zone ?: 'Central' }}</span>
                            </div>
                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3 vote-trigger-btn"
                                data-pandal="{{ $p->name }}"
                                data-participant-id="{{ $p->id }}"
                                data-award-id="{{ $lightingAward?->id ?? '' }}">
                                <i class="bi bi-hand-thumbs-up me-1"></i> Vote
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Explore All Pujas CTA Banner -->
    <div class="mt-5 p-4 rounded-4 shadow-sm text-center text-md-start" style="background: linear-gradient(135deg, #fdf5ea 0%, #fae8d0 100%); border: 2px dashed #d4a373;">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <span class="badge bg-danger text-white rounded-pill px-3 py-1 mb-2 fw-semibold">
                    <i class="bi bi-grid-3x3-gap-fill me-1"></i> Full Directory
                </span>
                <h4 class="fw-bold mb-1" style="color: #8c0c16;">Explore All Registered Durga Pujas</h4>
                <p class="text-muted small mb-0">Search all pandals with real-time auto-suggestions, lazy loading directory, and live community voting.</p>
            </div>
            <div class="col-md-4 text-center text-md-end">
                <a href="{{ isset($event) && $event->slug ? route('events.all-pujas', $event->slug) : route('all-pujas') }}" class="btn btn-vote-pill px-4 py-2 text-decoration-none">
                    <span>Browse All Pujas</span>
                    <span class="vote-icon-badge">
                        <i class="bi bi-arrow-right"></i>
                    </span>
                </a>
            </div>
        </div>
    </div>
</div>
@endif
