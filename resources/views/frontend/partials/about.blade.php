@if (!empty($event->about_text))
<section class="durgo-about-section text-center py-5" id="about">
    <!-- Main Container -->
    <div class="container position-relative z-2">
        <!-- Title -->
        <h2 class="durgo-title">About Dibrugarh Durgo Utsav {{ $event->year ?? 2026 }}</h2>
        <!-- Ornamental Divider (Lines + Ethnic Mandala Motif) -->
        <div class="ornamental-divider-wrapper" aria-hidden="true">
            <span class="divider-line"></span>
            <div class="mandala-center-motif">
                <!-- Ethnic Mandala Flower Motif SVG -->
                <svg width="34" height="34" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Outer Petal Radiance -->
                    <circle cx="20" cy="20" r="17" stroke="#caa267" stroke-width="1.2" stroke-dasharray="2 2" />
                    <!-- 8 Direction Floral Petals -->
                    <path
                        d="M20 3 C21.5 9 24 11 30 11 C24 13 22 15.5 20 22 C18 15.5 16 13 10 11 C16 11 18.5 9 20 3 Z"
                        fill="#caa267" fill-opacity="0.9" />
                    <path
                        d="M20 37 C21.5 31 24 29 30 29 C24 27 22 24.5 20 18 C18 24.5 16 27 10 29 C16 29 18.5 31 20 37 Z"
                        fill="#caa267" fill-opacity="0.9" />
                    <!-- Diagonal Diamond Accent Petals -->
                    <rect x="17" y="17" width="6" height="6" transform="rotate(45 20 20)" fill="#6a070e" />
                    <!-- Central Golden Core -->
                    <circle cx="20" cy="20" r="3.2" fill="#caa267" />
                    <circle cx="20" cy="20" r="1.3" fill="#6a070e" />
                </svg>
            </div>
            <span class="divider-line right-line"></span>
        </div>
        <!-- Description Paragraph -->
        <div class="durgo-description">
            {!! $event->about_text !!}
        </div>
    </div>
</section>
@endif