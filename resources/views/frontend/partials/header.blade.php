<header class="header-wrapper">
    <nav class="navbar navbar-expand-lg py-2">
        <div class="container-fluid px-lg-5 px-3">

            @php
                $isAllPujasPage = request()->routeIs('events.all-pujas');
                $isVotePage = request()->routeIs('events.vote');
                $isVoteSuccessPage = request()->routeIs('events.vote-success');
                $isHomePage = !$isAllPujasPage && !$isVotePage && !$isVoteSuccessPage;
                $homeUrl = isset($event) && $event->slug ? route('events.show', ['event' => $event->slug]) : route('home');
                $allPujasUrl = isset($event) && $event->slug ? route('events.all-pujas', ['event' => $event->slug]) : route('home');
            @endphp
            <!-- Logo Branding -->
            <a class="brand-logo-container" href="{{ $isHomePage ? '#home' : $homeUrl . '#home' }}" id="brandLogo">
                <img src="{{ asset('event-assets/images/logo-dibru.png') }}" alt="Dibrugarh 24x7"
                    class="img-fluid navbar-brand-logo">
            </a>

            <!-- Responsive Mobile Hamburger Toggle -->
            <button class="navbar-toggler ms-auto me-lg-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarFestiveMenu" aria-controls="navbarFestiveMenu" aria-expanded="false"
                aria-label="Toggle navigation">
                <i class="bi bi-list fs-4"></i>
            </button>

            <!-- Navbar Collapsible Menu (Centered links) -->
            <div class="collapse navbar-collapse" id="navbarFestiveMenu">
                <ul class="navbar-nav mx-auto custom-nav my-3 my-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ $isHomePage ? 'active' : '' }}" data-target="home"
                            href="{{ $isHomePage ? '#home' : $homeUrl . '#home' }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $isAllPujasPage ? 'active' : '' }}" href="{{ $allPujasUrl }}">All
                            Pujas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-target="awards-announced"
                            href="{{ $isHomePage ? '#awards-announced' : $homeUrl . '#awards-announced' }}">Winners</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-target="gallery"
                            href="{{ $isHomePage ? '#gallery' : $homeUrl . '#gallery' }}">Gallery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-target="events"
                            href="{{ $isHomePage ? '#events' : $homeUrl . '#events' }}">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-target="about"
                            href="{{ $isHomePage ? '#about' : $homeUrl . '#about' }}">About</a>
                    </li>
                </ul>

                <!-- Right Action Button: Vote Now / Voting Closed -->
                @php
                    $isVotingOpen = isset($event) && $event->isVotingOpen();
                @endphp
                <div class="d-flex align-items-center mt-3 mt-lg-0">
                    @if ($isVotingOpen)
                        @if ($isHomePage)
                            <a href="{{ $allPujasUrl }}" class="btn btn-vote-pill w-100 w-lg-auto text-decoration-none"
                                id="btnVoteModal">
                                <span>Vote Now</span>
                                <span class="vote-icon-badge">
                                    <i class="bi bi-hand-index-thumb-fill"></i>
                                </span>
                            </a>
                        @elseif ($isVotePage)
                            <a href="#voting-form-section" class="btn btn-vote-pill w-100 w-lg-auto text-decoration-none"
                                id="btnVoteModalNav">
                                <span>Vote Now</span>
                                <span class="vote-icon-badge">
                                    <i class="bi bi-hand-index-thumb-fill"></i>
                                </span>
                            </a>
                        @else
                            <a href="{{ $allPujasUrl }}" class="btn btn-vote-pill w-100 w-lg-auto text-decoration-none"
                                id="btnVoteModalNav">
                                <span>Vote Now</span>
                                <span class="vote-icon-badge">
                                    <i class="bi bi-hand-index-thumb-fill"></i>
                                </span>
                            </a>
                        @endif
                    @else
                        <button class="btn btn-secondary rounded-pill w-100 w-lg-auto opacity-75" id="btnVoteModalClosed"
                            type="button" disabled>
                            <span>Voting Closed</span>
                            <span class="badge bg-dark rounded-pill ms-2">
                                <i class="bi bi-lock-fill"></i>
                            </span>
                        </button>
                    @endif
                </div>
            </div>

        </div>
    </nav>

    @if ($isVotingOpen && $isHomePage)
        <a href="{{ $allPujasUrl }}" class="btn btn-vote-pill vote-button-float text-decoration-none" id="btnVoteModal">
            <span>Vote Now</span>
            <span class="vote-icon-badge">
                <i class="bi bi-hand-index-thumb-fill"></i>
            </span>
        </a>
    @endif
</header>