<x-layouts.microsite
    :meta-title="($event->meta_title ?: 'All Pujas Directory — ' . ($event->display_name ?? 'Dibrugarh Durgo Utsav 2026'))"
    :meta-description="$event->meta_description ?: 'Explore the complete directory of registered Sarbajanin Durga Pujas across Dibrugarh with interactive search, auto-suggestions, and live voting.'"
    :og-image="$event->og_image ?? null"
    :use-all-pujas-script="true"
>
    <!-- Top Navigation Header -->
    @include('frontend.partials.header')

    <!-- Main Content -->
    <main>

        <!-- Dynamic Dataset Bridge for JavaScript Filtering & Lazy Loading -->
        <script>
            window.dynamicPujasData = @json($pujasData);
        </script>

        <!-- Hero Section with Search & Auto-Suggestion -->
        <section class="allpujas-hero text-center">
            <div class="container position-relative z-2">

                <!-- Title & Tagline -->
                <h1 class="allpujas-title mb-1">Dibrugarh Durga Pujas</h1>
                <p class="allpujas-subtitle mb-3">Search and vote for your favourite puja committee</p>

                @if ($totalPujasCount > 0)
                    <!-- Search Field with Auto Suggestion -->
                    <div class="search-box-wrapper">
                        <div class="search-input-container">
                            <span class="search-icon-decor" aria-hidden="true">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" id="pujaSearchInput" class="search-field-input"
                                placeholder="Search by puja name, location, or theme..."
                                autocomplete="off" aria-label="Search all Dibrugarh pujas">
                            <button type="button" id="searchClearBtn" class="search-clear-btn" aria-label="Clear search">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                            <button type="button" class="search-submit-btn d-none d-sm-inline-flex" id="searchSubmitBtn">
                                <span>Search</span>
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>

                        <!-- Auto Suggestion Floating Dropdown -->
                        <div id="searchSuggestionsDropdown" class="autosuggest-dropdown text-start" role="listbox">
                            <div class="autosuggest-header">
                                <span><i class="bi bi-lightning-charge-fill me-1 text-warning"></i> Suggestions</span>
                                <span id="suggestionsHeaderCount" class="text-muted">0 matches</span>
                            </div>
                            <ul id="suggestionsList" class="autosuggest-list">
                                <!-- Populated dynamically via allpujas.js -->
                            </ul>
                            <div class="autosuggest-footer">
                                <span><i class="bi bi-arrow-down-up me-1"></i> Navigate with &uarr; &darr;, Enter to pick</span>
                                <span>Esc to close</span>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </section>

        @if ($totalPujasCount > 0)
            <!-- Filter Chips & Sorting Control Bar -->
            <section class="pujas-controls-bar sticky-top">
                <div class="container">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

                        <!-- Zone / Location Filter Chips -->
                        <div class="filter-chips-wrap">
                            <button class="filter-chip-btn active" data-zone="all">
                                <i class="bi bi-grid-fill"></i> All Pujas
                                <span class="chip-count">{{ $totalPujasCount }}</span>
                            </button>
                            @foreach ($filterZones as $fz)
                                @if ($fz['count'] > 0)
                                    <button class="filter-chip-btn" data-zone="{{ $fz['name'] }}">
                                        <i class="bi {{ $fz['icon'] }}" @if(!empty($fz['color'])) style="color: {{ $fz['color'] }};" @endif></i> {{ $fz['label'] }}
                                        <span class="chip-count">{{ $fz['count'] }}</span>
                                    </button>
                                @endif
                            @endforeach
                        </div>

                        <!-- Result Counter & Sort By Dropdown -->
                        <div
                            class="d-flex align-items-center justify-content-between justify-content-md-end gap-3 flex-shrink-0">
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-semibold"
                                id="resultCountBadge">
                                Showing {{ min(12, $totalPujasCount) }} of {{ $totalPujasCount }} Pujas
                            </span>

                            <div class="d-flex align-items-center gap-2">
                                <label for="pujasSortSelect"
                                    class="small text-muted fw-bold text-nowrap mb-0 d-none d-sm-inline">Sort:</label>
                                <select class="form-select form-select-sm rounded-pill border" id="pujasSortSelect"
                                    style="min-width: 160px;">
                                    <option value="popular" selected>Most Votes</option>
                                    <option value="name-asc">Name (A &rarr; Z)</option>
                                    <option value="name-desc">Name (Z &rarr; A)</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
        @endif

        <!-- All Pujas Cards Grid Section with Lazy Load -->
        <section class="pujas-grid-section py-4">
            <div class="container">
                <!-- Dynamic Card Grid -->
                <div class="row g-4" id="pujasCardsGrid">
                    @forelse (collect($pujasData)->take(12) as $puja)
                        <div class="col-12 col-md-6 col-lg-4 card-appear-animate" id="puja-card-{{ $puja['id'] }}" data-pandal="{{ $puja['name'] }}" data-id="{{ $puja['id'] }}">
                            <div class="vote-card h-100 p-2 p-md-3 d-flex flex-column">
                                <div class="position-relative mb-3">
                                    <span class="card-num-badge">#{{ sprintf('%02d', $puja['id']) }}</span>
                                    <img src="{{ $puja['image'] }}"
                                         alt="{{ $puja['name'] }}"
                                         class="img-fluid rounded-3 w-100"
                                         style="height: 200px; object-fit: cover;"
                                         loading="lazy"
                                         onerror="this.onerror=null;this.src='{{ asset('event-assets/images/hero.png') }}';">
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-baseline mb-1">
                                    <h5 class="fw-bold mb-0 text-dark fs-6 fs-md-5">{{ $puja['name'] }}</h5>
                                </div>

                                <p class="text-muted small mb-2">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $puja['location'] }}
                                </p>

                                <div class="small text-secondary mb-3 fst-italic" style="font-size: 0.82rem; line-height: 1.4;">
                                    <i class="bi bi-stars text-warning me-1"></i> Theme: "{{ $puja['theme'] }}"
                                </div>

                                <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-auto">
                                    <div>
                                        <small class="text-muted d-block" style="font-size: 0.72rem;">Current Count</small>
                                        <span class="fw-bold fs-6 text-danger" id="count-puja-{{ $puja['id'] }}">{{ number_format($puja['votes']) }} votes</span>
                                    </div>
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3 vote-trigger-btn"
                                        data-pandal="{{ $puja['name'] }}" data-puja-id="{{ $puja['id'] }}">
                                        <i class="bi bi-hand-thumbs-up me-1"></i> Vote
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="empty-pujas-box shadow-sm">
                                <div class="empty-icon-circle">
                                    <i class="bi bi-search"></i>
                                </div>
                                <h3 class="fw-bold mb-2 text-dark">No Pujas Found</h3>
                                <p class="text-muted mb-4">No registered pandals available yet.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                @if ($totalPujasCount > 0)
                    <!-- Lazy Load Spinner & Load More Controls -->
                    <div class="lazy-load-section" id="lazyLoadTriggerSection">
                        <div id="lazySpinner" class="lazy-spinner-container" style="display: none;">
                            <span class="pulsing-dots">
                                <span></span><span></span><span></span>
                            </span>
                            <span>Loading more Dibrugarh pandals...</span>
                        </div>

                        <button id="btnLoadMore" class="btn btn-load-more-pujas">
                            <i class="bi bi-chevron-down me-1"></i> Load More Pujas
                        </button>

                        <div id="allLoadedIndicator" class="text-muted small py-3" style="display: none;">
                            <i class="bi bi-check-circle-fill text-success me-1"></i> You've explored all {{ $totalPujasCount }} {{ $event->display_name ?? 'Dibrugarh' }}
                            Pandals • Don't forget to cast your vote!
                        </div>

                        <!-- Intersection Observer Sentinel -->
                        <div id="lazyLoadSentinel" style="height: 20px; width: 100%;"></div>
                    </div>
                @endif
            </div>
        </section>

    </main>

    <!-- Footer -->
    @include('frontend.partials.footer')

    <!-- Interactive Lightbox & Voting Modals -->
    @include('frontend.partials.modals')

</x-layouts.microsite>
