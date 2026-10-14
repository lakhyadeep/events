@if (isset($publishedWinners) && $publishedWinners->isNotEmpty())
<section class="awards-announced-section py-5" id="">
    <div class="container">
        <a href="{{ route('all-pujas') }}"
            class="awards-announced-header-box text-center d-block text-decoration-none mb-5"
            title="Click to view all Dibrugarh Durga Pujas">
            <!-- Top Corner Click Indicator Badge -->
            <div class="awards-click-corner-badge" aria-hidden="true">
                <span>View All Pujas</span>
                <i class="bi bi-arrow-up-right-circle-fill ms-1"></i>
            </div>

            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3 shadow-sm">
                OFFICIAL ANNOUNCEMENT • SHARAD SAMMAN {{ $event->year ?? date('Y') }}
            </span>
            <h2 class="display-6 fw-bold mb-2 text-white">
                🏆 Sharad Samman {{ $event->year ?? date('Y') }} — Official Award Winners
                <i class="bi bi-arrow-right-circle-fill text-warning ms-1 click-arrow-icon" aria-hidden="true"></i>
            </h2>
            <p class="lead text-white-50 mx-auto mb-4" style="max-width: 780px; font-size: 1.05rem;">
                Public voting and official jury assessments have concluded! Celebrating the crowned champions of
                Dibrugarh's finest pandals, idols, and lightings.
            </p>

            <!-- Key Stats Bar -->
            <div class="d-flex flex-wrap justify-content-center gap-3 pt-2 border-top border-warning border-opacity-25">
                <div class="px-3 py-1 rounded-pill bg-white bg-opacity-10 text-white small">
                    <i class="bi bi-check-circle-fill text-warning me-1"></i>
                    <strong>48920+</strong> Verified
                    Votes Cast
                </div>
                <div class="px-3 py-1 rounded-pill bg-white bg-opacity-10 text-white small">
                    <i class="bi bi-award-fill text-warning me-1"></i> <strong>4 Flagship</strong>
                    Award
                    Categories
                </div>
                <div class="px-3 py-1 rounded-pill bg-white bg-opacity-10 text-white small">
                    <i class="bi bi-shield-check text-warning me-1"></i> <strong>Jury Certified</strong> Results
                </div>
            </div>
        </a>
    </div>

</section>
@endif