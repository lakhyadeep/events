<footer class="site-footer" id="siteFooter">

    <!-- Left Alpana / Floral Corner SVG Overlay -->
    <div class="alpana-watermark alpana-left" aria-hidden="true">
        <svg width="200" height="150" viewBox="0 0 200 150" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M-20 75 C40 10 90 20 130 75 C90 130 40 140 -20 75 Z" stroke="#caa267" stroke-width="2"
                stroke-dasharray="3 3" />
            <path d="M-30 75 C20 25 70 35 100 75 C70 115 20 125 -30 75 Z" stroke="#caa267" stroke-width="1.5" />
            <circle cx="50" cy="75" r="30" stroke="#caa267" stroke-width="1.5" stroke-dasharray="4 2" />
            <circle cx="50" cy="75" r="14" fill="#caa267" fill-opacity="0.2" stroke="#caa267" stroke-width="1.2" />
            <path d="M50 45 L50 105 M20 75 L80 75" stroke="#caa267" stroke-width="1.2" />
            <circle cx="20" cy="75" r="3" fill="#caa267" />
            <circle cx="80" cy="75" r="3" fill="#caa267" />
            <circle cx="50" cy="45" r="3" fill="#caa267" />
            <circle cx="50" cy="105" r="3" fill="#caa267" />
        </svg>
    </div>

    <!-- Right Alpana / Floral Corner SVG Overlay -->
    <div class="alpana-watermark alpana-right" aria-hidden="true">
        <svg width="200" height="150" viewBox="0 0 200 150" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M-20 75 C40 10 90 20 130 75 C90 130 40 140 -20 75 Z" stroke="#caa267" stroke-width="2"
                stroke-dasharray="3 3" />
            <path d="M-30 75 C20 25 70 35 100 75 C70 115 20 125 -30 75 Z" stroke="#caa267" stroke-width="1.5" />
            <circle cx="50" cy="75" r="30" stroke="#caa267" stroke-width="1.5" stroke-dasharray="4 2" />
            <circle cx="50" cy="75" r="14" fill="#caa267" fill-opacity="0.2" stroke="#caa267" stroke-width="1.2" />
            <path d="M50 45 L50 105 M20 75 L80 75" stroke="#caa267" stroke-width="1.2" />
            <circle cx="20" cy="75" r="3" fill="#caa267" />
            <circle cx="80" cy="75" r="3" fill="#caa267" />
            <circle cx="50" cy="45" r="3" fill="#caa267" />
            <circle cx="50" cy="105" r="3" fill="#caa267" />
        </svg>
    </div>

    <!-- Main Upper Footer Content -->
    <div class="container footer-main">
        <div class="row align-items-center gy-3">

            <!-- Left: Dibrugarh 24x7 Logo -->
            <div class="col-12 col-lg-3 text-center text-lg-start">
                <a href="{{ $event->external_link ?: '#home' }}" class="dibrugarh-brand"
                    title="{{ $event->display_name ?? 'Dibrugarh 24x7' }}">
                    <img src="{{ asset('event-assets/images/logo-dibru.png') }}"
                        alt="{{ $event->display_name ?? 'Dibrugarh 24x7' }}" class="img-fluid footer-brand-logo">
                </a>
                @if ($event->contact_phone || $event->contact_email)
                    <div class="mt-2 text-muted small" style="font-size: 0.78rem;">
                        @if ($event->contact_phone)
                            <div><i class="bi bi-telephone-fill me-1 text-danger"></i> {{ $event->contact_phone }}</div>
                        @endif
                        @if ($event->contact_email)
                            <div><i class="bi bi-envelope-fill me-1 text-danger"></i> {{ $event->contact_email }}</div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Center: Navigation Links -->
            <div class="col-12 col-lg-6 text-center">
                @php
                    $isAllPujasPage = request()->routeIs('events.all-pujas');
                    $isVotePage = request()->routeIs('events.vote');
                    $isVoteSuccessPage = request()->routeIs('events.vote-success');
                    $isHomePage = !$isAllPujasPage && !$isVotePage && !$isVoteSuccessPage;
                    $homeUrl = isset($event) && $event->slug ? route('events.show', ['event' => $event->slug]) : route('home');
                    $allPujasUrl = isset($event) && $event->slug ? route('events.all-pujas', ['event' => $event->slug]) : route('home');
                @endphp
                <ul class="footer-nav">
                    <li><a href="{{ $isHomePage ? '#home' : $homeUrl . '#home' }}"
                            class="nav-link-item {{ $isHomePage ? 'active' : '' }}" data-page="Home">Home</a></li>
                    <li class="nav-sep">|</li>
                    <li><a href="{{ $allPujasUrl }}" class="nav-link-item {{ $isAllPujasPage ? 'active' : '' }}"
                            data-page="All Pujas">All Pujas</a></li>
                    <li class="nav-sep">|</li>
                    <li><a href="{{ $allPujasUrl }}" class="nav-link-item {{ $isVotePage ? 'active' : '' }}"
                            data-page="Vote">Vote</a></li>
                    <li class="nav-sep">|</li>
                    <li><a href="{{ $isHomePage ? '#gallery' : $homeUrl . '#gallery' }}" class="nav-link-item"
                            data-page="Gallery">Gallery</a></li>
                    <li class="nav-sep">|</li>
                    <li><a href="{{ $isHomePage ? '#events' : $homeUrl . '#events' }}" class="nav-link-item"
                            data-page="Events">Events</a></li>
                    <li class="nav-sep">|</li>
                    <li><a href="{{ $isHomePage ? '#about' : $homeUrl . '#about' }}" class="nav-link-item"
                            data-page="About">About</a></li>
                </ul>
            </div>

            <!-- Right: Social Media Icons -->
            <div class="col-12 col-lg-3 text-center text-lg-end">
                <div class="footer-socials">
                    <!-- Facebook -->
                    <a href="https://www.facebook.com/dibrugarh24x7" target="_blank" rel="noopener noreferrer"
                        class="social-btn social-facebook" title="Facebook"
                        aria-label="Visit Dibrugarh 24x7 on Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <!-- Instagram -->
                    <a href="https://www.instagram.com/dibrugarh24x7" target="_blank" rel="noopener noreferrer"
                        class="social-btn social-instagram" title="Instagram"
                        aria-label="Visit Dibrugarh 24x7 on Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <!-- YouTube -->
                    <a href="https://youtube.com/@dibrugarh24x7?si=-r7HwWOF3GwMIjuS" target="_blank"
                        rel="noopener noreferrer" class="social-btn social-youtube" title="YouTube"
                        aria-label="Subscribe to Dibrugarh 24x7 on YouTube">
                        <i class="bi bi-youtube"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Bottom Sub-Footer Bar (Rich Crimson / Maroon) -->
    <div class="sub-footer">
        <div class="container">
            <div class="row align-items-center">
                <!-- Copyright Notice -->
                <div class="col text-center mb-2 mb-md-0">
                    <span>&copy; {{ $event->year ?? 2026 }} Dibrugarh 24x7 - A unit of Tea City Media House. All Rights
                        Reserved.</span>
                    <div>
                        <span>Website designed and developed by <a href="https://digitalpilot.co.in/" target="_blank"
                                style="font-weight: 500; color: #caa267;">Digital Pilot.</a></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</footer>