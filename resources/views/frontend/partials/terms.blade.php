<section class="terms-conditions-section py-5 position-relative" id="terms">
    <div class="container">
        <div class="text-center mb-4">
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2 fw-bold text-uppercase mb-2"
                style="letter-spacing: 1px; font-size: 0.75rem;">
                <i class="bi bi-shield-check me-1"></i> Official Guidelines
            </span>
            <h2 class="fw-bold fs-2 text-dark mb-2">Terms &amp; Conditions</h2>
            <p class="text-muted small mx-auto" style="max-width: 600px;">
                Please read the official voting rules and participation guidelines for {{ $event->display_name ?? 'Dibrugarh Durgo Utsav 2026' }}.
            </p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="terms-list-wrapper">
                    @if (!empty($event->terms_and_conditions))
                        <div class="p-4 bg-white rounded-4 shadow-sm border border-light-subtle terms-dynamic-content" style="font-size: 0.95rem; line-height: 1.7;">
                            {!! $event->terms_and_conditions !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
