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
                @if ($event->timelineItems && $event->timelineItems->isNotEmpty())
                    @foreach ($event->timelineItems as $item)
                        @php
                            $statusVal = $item->status?->value ?? (string)$item->status;
                            $isActive = in_array($statusVal, ['active', 'ongoing', 'current']);
                        @endphp
                        <div class="col-6 col-md-3 timeline-step {{ $isActive ? 'active-milestone' : '' }}" data-milestone="{{ $item->title }}">
                            <div class="step-circle" title="{{ $item->title }}">
                                @if ($statusVal === 'completed')
                                    <svg viewBox="0 0 24 24" width="20" height="20">
                                        <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z" fill="#6a070e" />
                                    </svg>
                                @elseif ($isActive)
                                    <svg viewBox="0 0 24 24" width="20" height="20">
                                        <circle cx="12" cy="12" r="6" fill="#b70d18" />
                                        <circle cx="12" cy="12" r="10" stroke="#b70d18" stroke-width="2" fill="none" stroke-dasharray="3 3" />
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" width="20" height="20">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" fill="none" stroke="#6a070e" stroke-width="2" />
                                        <line x1="16" y1="2" x2="16" y2="6" stroke="#6a070e" stroke-width="2" stroke-linecap="round" />
                                        <line x1="8" y1="2" x2="8" y2="6" stroke="#6a070e" stroke-width="2" stroke-linecap="round" />
                                        <line x1="3" y1="10" x2="21" y2="10" stroke="#6a070e" stroke-width="2" />
                                    </svg>
                                @endif
                            </div>
                            <div class="step-pin-dot"></div>
                            <h3 class="step-title">{{ $item->title }}</h3>
                            <p class="step-date mb-1">{{ $item->milestone_date }}</p>
                            @if ($item->description)
                                <p class="step-desc text-muted px-2" style="font-size: 0.74rem;">{{ $item->description }}</p>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</section>
