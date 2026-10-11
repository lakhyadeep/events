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
                    @else
                        <ul class="terms-bullet-list list-unstyled mb-0">
                            <li class="terms-bullet-item d-flex align-items-start gap-3 mb-3 border-bottom border-light">
                                <span class="terms-check-icon text-danger flex-shrink-0 mt-1"><i class="bi bi-check-circle-fill fs-5"></i></span>
                                <div>
                                    <strong class="text-dark">1. One Vote Per Category:</strong> Each voter is entitled to cast only 1 vote per award category (Best Puja, Best Pratima, Best Lighting) during the active voting period.
                                </div>
                            </li>
                            <li class="terms-bullet-item d-flex align-items-start gap-3 mb-3 border-bottom border-light">
                                <span class="terms-check-icon text-danger flex-shrink-0 mt-1"><i class="bi bi-check-circle-fill fs-5"></i></span>
                                <div>
                                    <strong class="text-dark">2. Fair Play &amp; Anti-Spam Protection:</strong> All votes are verified in real time. Automated bot scripts, IP manipulation, or duplicate spam votes will be automatically detected and purged.
                                </div>
                            </li>
                            <li class="terms-bullet-item d-flex align-items-start gap-3 mb-3 border-bottom border-light">
                                <span class="terms-check-icon text-danger flex-shrink-0 mt-1"><i class="bi bi-check-circle-fill fs-5"></i></span>
                                <div>
                                    <strong class="text-dark">3. Nominated Committee Eligibility:</strong> Only officially registered and shortlisted Sarbajanin Durga Puja committees within Dibrugarh municipality boundaries are eligible for voting.
                                </div>
                            </li>
                            <li class="terms-bullet-item d-flex align-items-start gap-3 mb-3 border-bottom border-light">
                                <span class="terms-check-icon text-danger flex-shrink-0 mt-1"><i class="bi bi-check-circle-fill fs-5"></i></span>
                                <div>
                                    <strong class="text-dark">4. Jury &amp; Public Weighted Score:</strong> Winners of Dibrugarh Sharad Samman 2026 are determined by a weighted combination of verified public voting and independent jury panel evaluations.
                                </div>
                            </li>
                            <li class="terms-bullet-item d-flex align-items-start gap-3 mb-3 border-bottom border-light">
                                <span class="terms-check-icon text-danger flex-shrink-0 mt-1"><i class="bi bi-check-circle-fill fs-5"></i></span>
                                <div>
                                    <strong class="text-dark">5. Safety &amp; Environmental Compliance:</strong> Participating committees must strictly adhere to eco-friendly idol guidelines, fire safety, noise limiters, and crowd management standards.
                                </div>
                            </li>
                            <li class="terms-bullet-item d-flex align-items-start gap-3 mb-3 border-bottom border-light">
                                <span class="terms-check-icon text-danger flex-shrink-0 mt-1"><i class="bi bi-check-circle-fill fs-5"></i></span>
                                <div>
                                    <strong class="text-dark">6. Voting Timeline Adherence:</strong> Online voting schedule is regulated strictly by the official timeline. Votes received after the cutoff will not be accepted.
                                </div>
                            </li>
                            <li class="terms-bullet-item d-flex align-items-start gap-3">
                                <span class="terms-check-icon text-danger flex-shrink-0 mt-1"><i class="bi bi-check-circle-fill fs-5"></i></span>
                                <div>
                                    <strong class="text-dark">7. Final Decision Authority:</strong> The final decision of the Organising Committee and Jury Board is binding and non-contestable.
                                </div>
                            </li>
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
