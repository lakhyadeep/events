@if (!empty($event->about_text))
    <section class="durgo-utsav-section text-center position-relative" id="about">
        <!-- Main Container -->
        <div class="container position-relative z-2">
            <!-- Title -->
            <h2 class="durgo-title">About Dibrugarh Durgotsav Awards {{ $event->year ?? 2026 }}</h2>
            <!-- Ornamental Divider (Lines + Ethnic Mandala Motif) -->
            <div class="ornamental-divider-wrapper" aria-hidden="true">
                <span class="divider-line"></span>
                <span class="divider-icon">
                    <!-- Traditional Floral Mandala Accent Motif -->
                    <svg class="mandala-symbol" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="9.5" y="9.5" width="5" height="5" fill="#8c0c16" transform="rotate(45 12 12)" />
                        <!-- Cardinal Petals -->
                        <path d="M12 2C12.8 4.2 13.5 6.5 12 8.5C10.5 6.5 11.2 4.2 12 2Z" fill="#8c0c16" />
                        <path d="M12 22C12.8 19.8 13.5 17.5 12 15.5C10.5 17.5 11.2 19.8 12 22Z" fill="#8c0c16" />
                        <path d="M2 12C4.2 12.8 6.5 13.5 8.5 12C6.5 10.5 4.2 11.2 2 12Z" fill="#8c0c16" />
                        <path d="M22 12C19.8 12.8 17.5 13.5 15.5 12C17.5 10.5 19.8 11.2 22 12Z" fill="#8c0c16" />
                        <!-- Diagonal Petals -->
                        <circle cx="6.5" cy="6.5" r="1.5" fill="#c68a4c" />
                        <circle cx="17.5" cy="6.5" r="1.5" fill="#c68a4c" />
                        <circle cx="6.5" cy="17.5" r="1.5" fill="#c68a4c" />
                        <circle cx="17.5" cy="17.5" r="1.5" fill="#c68a4c" />
                    </svg>
                </span>
                <span class="divider-line right-line"></span>
            </div>
            <!-- Description Paragraph -->
            <div class="durgo-description">
                {!! $event->about_text !!}
            </div>
        </div>
    </section>
@endif