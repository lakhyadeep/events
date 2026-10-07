<header class="header-wrapper">
        <nav class="navbar navbar-expand-lg py-2">
            <div class="container-fluid px-lg-5 px-3">

                <!-- Logo Branding -->
                <a class="brand-logo-container" href="#home" id="brandLogo">
                    <img src="{{ asset('event-assets/images/logo-dibru.png') }}" alt="Dibrugarh 24x7" class="img-fluid navbar-brand-logo">
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
                            <a class="nav-link active" data-target="home" href="#home">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-target="pujas" href="#pujas">Pujas</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-target="gallery" href="#gallery">Gallery</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-target="events" href="#events">Events</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-target="about" href="#about">About</a>
                        </li>
                    </ul>

                    <!-- Right Action Button: Vote Now -->
                    <div class="d-flex align-items-center mt-3 mt-lg-0">
                        <button class="btn btn-vote-pill w-100 w-lg-auto" id="btnVoteModal" type="button"
                            data-bs-toggle="modal" data-bs-target="#voteModal">
                            <span>Vote Now</span>
                            <span class="vote-icon-badge">
                                <i class="bi bi-hand-index-thumb-fill"></i>
                            </span>
                        </button>
                    </div>
                </div>

            </div>
        </nav>
    </header>
