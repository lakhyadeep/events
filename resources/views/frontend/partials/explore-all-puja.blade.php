@if (isset($publishedWinners) && $publishedWinners->isEmpty())
    <section class="explore-all-puja-section py-5" style="border-bottom: none;">
        <div class="container">
            <div class="p-4 rounded-4 shadow-sm text-center text-md-start"
                style="background: linear-gradient(135deg, #fdf5ea 0%, #fae8d0 100%); border: 2px dashed #d4a373;">
                <div class="row align-items-center g-3">
                    <div class="col-md-8">
                        <span class="badge bg-danger text-white rounded-pill px-3 py-1 mb-2 fw-semibold">
                            <i class="bi bi-grid-3x3-gap-fill me-1"></i> Full Directory
                        </span>
                        <h4 class="fw-bold mb-1" style="color: #8c0c16;">Explore All Durga Pujas in Dibrugarh</h4>
                        <p class="text-muted small mb-0">Search all pandals with real-time auto-suggestions, lazy loading
                            directory,
                            and live community voting.</p>
                    </div>
                    <div class="col-md-4 text-center text-md-end">
                        <a href="{{ isset($event) && $event->slug ? route('events.all-pujas', ['event' => $event->slug]) : route('home') }}"
                            class="btn btn-vote-pill px-4 py-2 text-decoration-none">
                            <span>Browse All Pujas</span>
                            <span class="vote-icon-badge">
                                <i class="bi bi-arrow-right"></i>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif