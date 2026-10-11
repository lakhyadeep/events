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
                @else
                    <!-- Fallback NODE 1: Voting Starts -->
                    <div class="col-6 col-md-3 timeline-step" data-milestone="Voting Starts">
                        <div class="step-circle" title="Voting Starts">
                            <svg viewBox="0 0 24 24">
                                <path d="M11 5L6 9H2v6h4l5 4V5z" fill="#6a070e" />
                            </svg>
                        </div>
                        <div class="step-pin-dot"></div>
                        <h3 class="step-title">Voting Starts</h3>
                        <p class="step-date">20 Sep 2026</p>
                    </div>

                    <!-- Fallback NODE 2: Voting Ends -->
                    <div class="col-6 col-md-3 timeline-step" data-milestone="Voting Ends">
                        <div class="step-circle" title="Voting Ends">
                            <svg viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" fill="none" stroke="#6a070e" stroke-width="2" />
                            </svg>
                        </div>
                        <div class="step-pin-dot"></div>
                        <h3 class="step-title">Voting Ends</h3>
                        <p class="step-date">30 Sep 2026</p>
                    </div>

                    <!-- Fallback NODE 3: Winners Announcement -->
                    <div class="col-6 col-md-3 timeline-step active-milestone" data-milestone="Winners Announcement">
                        <div class="step-circle" title="Winners Announcement">
                            <svg viewBox="0 0 24 24">
                                <path d="M6 3h12v7c0 3.31-2.69 6-6 6s-6-2.69-6-6V3z" fill="#6a070e" />
                            </svg>
                        </div>
                        <div class="step-pin-dot"></div>
                        <h3 class="step-title">Winners Announcement</h3>
                        <p class="step-date">5 Oct 2026</p>
                    </div>

                    <!-- Fallback NODE 4: Award Distribution -->
                    <div class="col-6 col-md-3 timeline-step" data-milestone="Award Distribution">
                        <div class="step-circle" title="Award Distribution">
                            <svg viewBox="0 0 24 24">
                                <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26" fill="#6a070e" />
                            </svg>
                        </div>
                        <div class="step-pin-dot"></div>
                        <h3 class="step-title">Award Distribution</h3>
                        <p class="step-date">9 Oct 2026</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
