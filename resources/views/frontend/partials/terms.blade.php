@if (!empty($event->terms_and_conditions))
    <section class="terms-conditions-section py-5 position-relative" id="terms">
        <style>
            .terms-bullet-item p {
                margin-bottom: 0;
            }
        </style>
        <div class="container">
            <div class="text-center mb-4">
                <span
                    class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2 fw-bold text-uppercase mb-2"
                    style="letter-spacing: 1px; font-size: 0.75rem;">
                    <i class="bi bi-shield-check me-1"></i>Official Guidelines — Dibrugarh Durgotsav Awards 2026
                </span>
                <h2 class="fw-bold fs-2 text-dark mb-2">Terms &amp; Conditions</h2>
                <p class="text-muted small mx-auto" style="max-width: 600px;">

                    Please read the official voting rules and participation guidelines before casting your vote.
                </p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="terms-list-wrapper">
                        @php
                            $rawTerms = $event->terms_and_conditions ?? '';
                            $termsItems = [];
                            if (preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $rawTerms, $matches) && !empty($matches[1])) {
                                $termsItems = $matches[1];
                            } else {
                                $clean = preg_replace('/<br\s*\/?>/i', "\n", $rawTerms);
                                $clean = preg_replace('/<\/p>/i', "\n", $clean);
                                $clean = preg_replace('/^<p[^>]*>/i', '', $clean);
                                $lines = array_filter(array_map('trim', explode("\n", $clean)), fn($line) => !empty($line));
                                $termsItems = array_values($lines);
                            }
                        @endphp

                        @if (!empty($termsItems))
                            <ul class="terms-bullet-list list-unstyled mb-0">
                                @foreach ($termsItems as $item)
                                    @php
                                        $cleanItem = preg_replace('/^<p[^>]*>|<\/p>$/i', '', trim($item));
                                        if (!str_contains($cleanItem, '<strong') && preg_match('/^(\d+\.[^:]*:)(.*)$/s', $cleanItem, $m)) {
                                            $cleanItem = '<strong class="text-dark">' . $m[1] . '</strong>' . $m[2];
                                        }
                                    @endphp
                                    <li
                                        class="terms-bullet-item d-flex align-items-start gap-3 {{ !$loop->last ? 'mb-3 border-bottom border-light' : '' }}">
                                        <span class="terms-check-icon text-danger flex-shrink-0 mt-1"><i
                                                class="bi bi-check-circle-fill fs-5"></i></span>
                                        <div>
                                            {!! $cleanItem !!}
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <br />
                            By participating in the voting process, voters agree to abide by these terms and conditions.
                        @else
                            <div class="terms-dynamic-content">
                                {!! $event->terms_and_conditions !!}

                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif