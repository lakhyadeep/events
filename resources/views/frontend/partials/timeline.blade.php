@if ($event->timelineItems && $event->timelineItems->isNotEmpty())
<!-- Event Timeline Section (Compact) -->
<section class="timeline-section" id="events">
    <div class="festive-pattern-bg" aria-hidden="true"></div>

    <div class="timeline-container">
        <!-- Section Title & Subtitle -->
        <h2 class="timeline-heading">Event Timeline</h2>
        <p class="timeline-subheading">Key dates for {{ $event->display_name ?? 'Dibrugarh Durgo Utsav 2026' }}</p>

        <!-- Horizontal Line & Steps Wrapper -->
        <div class="timeline-track-wrapper">
            <div class="timeline-line-base" aria-hidden="true">
                <div class="line-ornament ornament-1"></div>
                <div class="line-ornament ornament-2"></div>
            </div>

            <!-- Timeline Nodes Grid -->
            <div class="row g-0 justify-content-between align-items-start">
                @foreach ($event->timelineItems as $item)
                    @php
                        $statusVal = $item->status?->value ?? (string) $item->status;
                        $isActive = in_array($statusVal, ['active', 'ongoing', 'current']);
                    @endphp
                    <div class="col-6 col-md-3 timeline-step {{ $isActive ? 'active-milestone' : '' }}" data-milestone="{{ $item->title }}">
                        <div class="step-circle" title="{{ $item->title }}">
                            @if ($loop->iteration === 1 || str_contains(strtolower($item->title), 'start') || str_contains(strtolower($item->title), 'nomination') || str_contains(strtolower($item->title), 'open'))
                                <svg viewBox="0 0 24 24">
                                    <path d="M11 5L6 9H2v6h4l5 4V5z" />
                                    <path d="M15.54 8.46a5 5 0 0 1 0 7.07" stroke="#6a070e" stroke-width="2"
                                        stroke-linecap="round" fill="none" />
                                </svg>
                            @elseif ($loop->iteration === 2 || str_contains(strtolower($item->title), 'end') || str_contains(strtolower($item->title), 'jury') || str_contains(strtolower($item->title), 'inspect'))
                                <svg viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" fill="none" stroke="#6a070e"
                                        stroke-width="2" />
                                    <line x1="16" y1="2" x2="16" y2="6" stroke="#6a070e" stroke-width="2"
                                        stroke-linecap="round" />
                                    <line x1="8" y1="2" x2="8" y2="6" stroke="#6a070e" stroke-width="2"
                                        stroke-linecap="round" />
                                    <line x1="3" y1="10" x2="21" y2="10" stroke="#6a070e" stroke-width="2" />
                                </svg>
                            @elseif ($loop->iteration === 3 || str_contains(strtolower($item->title), 'winner') || str_contains(strtolower($item->title), 'voting'))
                                <svg viewBox="0 0 24 24">
                                    <path d="M6 3h12v7c0 3.31-2.69 6-6 6s-6-2.69-6-6V3z" />
                                    <line x1="12" y1="16" x2="12" y2="20" stroke="#6a070e" stroke-width="2" />
                                    <line x1="8" y1="21" x2="16" y2="21" stroke="#6a070e" stroke-width="2"
                                        stroke-linecap="round" />
                                </svg>
                            @else
                                <svg viewBox="0 0 24 24">
                                    <polygon
                                        points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26" />
                                </svg>
                            @endif
                        </div>
                        <div class="step-pin-dot"></div>
                        <h3 class="step-title">{{ $item->title }}</h3>
                        <p class="step-date">{{ $item->milestone_date }}</p>
                        @if ($item->description)
                            <p class="step-desc text-muted px-2" style="font-size: 0.74rem;">{{ $item->description }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif