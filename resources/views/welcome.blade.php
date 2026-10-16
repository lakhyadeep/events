<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dibrugarh 24x7 - Festive Awards & Navigation</title>

    <!-- Google Fonts: Poppins & Playfair Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,700;1,500&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ vasset('event-assets/css/style.css') }}">


</head>

<body>

    <!-- Top Navigation Header -->
    <header class="header-wrapper">
        <nav class="navbar navbar-expand-lg py-2">
            <div class="container-fluid px-lg-5 px-3">

                <!-- Logo Branding -->
                <a class="brand-logo-container" href="#home" id="brandLogo">
                    <img src="{{ vasset('event-assets/images/logo-dibru.png') }}" alt="Dibrugarh 24x7"
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
                            <a class="nav-link active" data-target="home" href="#home">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="allpujas.html">All Pujas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-target="awards-announced" href="#awards-announced">Winners</a>
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
                        <a href="allpujas.html" class="btn btn-vote-pill w-100 w-lg-auto text-decoration-none"
                            id="btnVoteModal">
                            <span>Vote Now</span>
                            <span class="vote-icon-badge">
                                <i class="bi bi-hand-index-thumb-fill"></i>
                            </span>
                        </a>
                    </div>
                </div>

            </div>
        </nav>
    </header>

    <!-- Context Showcase: Festive Awards Hero Section -->
    <main>
        <section class="hero-banner text-start" id="home">
            <div class="container py-4">
                <div class="d-inline-block text-center">
                    <img src="{{ vasset('event-assets/images/logo-award2026.png') }}" alt="Dibrugarh Sharad Samman 2026"
                        class="img-fluid hero-logo-img mb-3">
                    <p class="hero-tagline mb-0">
                        Celebrating Devotion, <br>Art, Culture &amp; Community
                    </p>
                </div>
            </div>
        </section>

        <section class="durgo-utsav-section text-center position-relative" id="about">
            <!-- Main Container -->
            <div class="container position-relative z-2">
                <!-- Title -->
                <h2 class="durgo-title">About Dibrugarh Durgo Utsav 2026</h2>
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
                <p class="durgo-description">
                    Dibrugarh Durgo Utsav celebrates the city's vibrant Durga Puja traditions, bringing together
                    devotion, creativity, art and community spirit. Discover the city's celebrated pujas, explore their
                    unique themes and cast your vote for the ones that inspire you.
                </p>
            </div>
        </section>

        <section class="sponsors-section-wrapper" id="sponsors">
            <div class="container sponsors-container">
                <!-- ==============================================
                     TOP ROW: ORGANISED BY, TITLE, HOSPITALITY, TROPHY
                     ============================================== -->
                <div class="row g-2 g-md-3 justify-content-center">

                    <!-- 1. ORGANISED BY: DIBRUGARH 24x7 -->
                    <div class="col-6 col-lg-3 divider-col-right">
                        <div class="sponsor-box" data-brand="Dibrugarh 24x7">
                            <div class="sponsor-title-wrap">
                                <span class="title-line"></span>
                                <span class="sponsor-title-text">ORGANISED BY</span>
                                <span class="title-line"></span>
                            </div>
                            <div class="sponsor-logo-canvas">
                                <div class="d-flex flex-column align-items-center">
                                    <img src="{{ vasset('event-assets/images/logo-dibru.png') }}" alt="" srcset="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. TITLE SPONSOR: JSB GROUP -->
                    <div class="col-6 col-lg-3 divider-col-right">
                        <div class="sponsor-box" data-brand="JSB Infrastructures">
                            <div class="sponsor-title-wrap">
                                <span class="title-line"></span>
                                <span class="sponsor-title-text">TITLE SPONSOR</span>
                                <span class="title-line"></span>
                            </div>
                            <div class="sponsor-logo-canvas">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <img src="{{ vasset('event-assets/images/logo-jsb.jpg') }}" alt="" srcset="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. HOSPITALITY SPONSOR: BADOCO TASTY TREAT -->
                    <div class="col-6 col-lg-3 divider-col-right">
                        <div class="sponsor-box" data-brand="Badoco Tasty Treat">
                            <div class="sponsor-title-wrap">
                                <span class="title-line"></span>
                                <span class="sponsor-title-text">HOSPITALITY SPONSOR</span>
                                <span class="title-line"></span>
                            </div>
                            <div class="sponsor-logo-canvas">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <img src="{{ vasset('event-assets/images/logo-tasty.jpg') }}" alt="" srcset="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. TROPHY SPONSOR: TONI&GUY -->
                    <div class="col-6 col-lg-3">
                        <div class="sponsor-box" data-brand="TONI&GUY Hairdressing">
                            <div class="sponsor-title-wrap">
                                <span class="title-line"></span>
                                <span class="sponsor-title-text">TROPHY SPONSOR</span>
                                <span class="title-line"></span>
                            </div>
                            <div class="sponsor-logo-canvas">
                                <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                    <img src="{{ vasset('event-assets/images/logo-tg.jpg') }}" alt="" srcset="">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ==============================================
                     MIDDLE DIVIDER: ASSOCIATE PARTNERS
                     ============================================== -->
                <div class="associate-divider-wrap">
                    <div class="long-divider-line"></div>
                    <div class="associate-title-pill">
                        <i class="bi bi-award-fill me-1" style="color: #ffcc00;"></i> ASSOCIATE PARTNERS
                    </div>
                    <div class="long-divider-line"></div>
                </div>

                <!-- ==============================================
                     BOTTOM ROW: ASSOCIATE PARTNERS (3 COLUMNS)
                     ============================================== -->
                <div class="row g-2 g-md-3 justify-content-center align-items-stretch">

                    <!-- 1. DIGITAL PILOT -->
                    <div class="col-12 col-md-3 partner-divider-right">
                        <div class="partner-box" data-brand="Digital Pilot">

                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ vasset('event-assets/images/logo-dp.png') }}" alt="" srcset="">

                            </div>
                        </div>
                    </div>

                    <!-- 2. CONCEPTCORE LEARNING -->
                    <div class="col-12 col-md-3 partner-divider-right">
                        <div class="partner-box" data-brand="ConceptCore Learning">

                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ vasset('event-assets/images/logo-cc.png') }}" alt="" srcset="">

                            </div>
                        </div>
                    </div>

                    <!-- 3. KINLEY -->
                    <div class="col-12 col-md-3">
                        <div class="partner-box" data-brand="Kinley - With Added Minerals">
                            <div class="d-flex flex-column align-items-center">
                                <div class="d-flex align-items-baseline position-relative">
                                    <img src="{{ vasset('event-assets/images/logo-kinley.png') }}" alt="" srcset="">
                                </div>
                                <div
                                    style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 0.55rem; color: #0077b6; letter-spacing: 0.4px; margin-top: 1px;">
                                    With Added Minerals
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </section>


        <!-- Official Awards Announced / Winners Showcase Section -->
        <section class="awards-announced-section py-5" id="awards-announced">
            <div class="container py-3">
                <!-- Section Header Box (Clickable redirect to allpujas.html) -->
                <a href="allpujas.html" class="awards-announced-header-box text-center  d-block text-decoration-none"
                    title="Click to view all Dibrugarh Durga Pujas">
                    <!-- Top Corner Click Indicator Badge -->
                    <div class="awards-click-corner-badge" aria-hidden="true">
                        <span>View All Pujas</span>
                        <i class="bi bi-arrow-up-right-circle-fill ms-1"></i>
                    </div>

                    <span
                        class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3 shadow-sm">
                        OFFICIAL ANNOUNCEMENT • SHARAD SAMMAN 2026
                    </span>
                    <h2 class="display-6 fw-bold mb-2 text-white">
                        🏆 Sharad Samman 2026 — Official Award Winners
                        <i class="bi bi-arrow-right-circle-fill text-warning ms-1 click-arrow-icon"
                            aria-hidden="true"></i>
                    </h2>
                    <p class="lead text-white-50 mx-auto mb-4" style="max-width: 780px; font-size: 1.05rem;">
                        Public voting and official jury assessments have concluded! Celebrating the crowned champions of
                        Dibrugarh's finest pandals, idols, and lightings.
                    </p>

                    <!-- Key Stats Bar -->
                    <div
                        class="d-flex flex-wrap justify-content-center gap-3 pt-2 border-top border-warning border-opacity-25">
                        <div class="px-3 py-1 rounded-pill bg-white bg-opacity-10 text-white small">
                            <i class="bi bi-check-circle-fill text-warning me-1"></i> <strong>48,920+</strong> Verified
                            Votes Cast
                        </div>
                        <div class="px-3 py-1 rounded-pill bg-white bg-opacity-10 text-white small">
                            <i class="bi bi-award-fill text-warning me-1"></i> <strong>4 Flagship</strong> Award
                            Categories
                        </div>
                        <div class="px-3 py-1 rounded-pill bg-white bg-opacity-10 text-white small">
                            <i class="bi bi-shield-check text-warning me-1"></i> <strong>Jury Certified</strong> Results
                        </div>
                    </div>


                </a>

                <!-- PINNACLE CATEGORY: BEST PUJA OVERALL WINNER -->
                <div class="category-crown-wrapper mb-5 mt-5 p-3 p-md-4 rounded-4 position-relative">
                    <div
                        class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 pb-2 border-bottom border-warning border-opacity-75">
                        <div class="d-flex align-items-center gap-2">

                            <h3 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                Best Puja Overall — Announced Winner
                                <span
                                    class="badge bg-warning bg-opacity-25 text-danger border border-warning rounded-pill px-2 py-1 fs-6 d-none d-sm-inline-flex">
                                    <i class="bi bi-stars me-1 text-warning"></i>Supreme Grand Champion
                                </span>
                            </h3>
                        </div>
                        <span
                            class="badge bg-warning bg-opacity-25 text-dark border border-warning rounded-pill px-3 py-2 small fw-semibold">
                            <i class="bi bi-trophy-fill text-danger me-1"></i> Maha Puraskar • Grand Champion Title
                        </span>
                    </div>

                    <div class="row justify-content-center">
                        <!-- Crown Grand Champion Winner (Horizontal Side-by-Side Card) -->
                        <div class="col-12 col-lg-10 col-xl-9">
                            <div class="winner-podium-card award-card-crown winner-horizontal h-100 position-relative">
                                <!-- Pinnacle Best of Best Top Ribbon Badge -->
                                <div class="pinnacle-ribbon-badge">
                                    <i class="bi bi-stars text-warning me-1"></i>
                                    <span>BEST OF BEST • GRAND CHAMPION</span>
                                    <i class="bi bi-stars text-warning ms-1"></i>
                                </div>

                                <div class="row g-0 align-items-stretch">
                                    <!-- Left Column: Photo, Overlay & Badges -->
                                    <div class="col-12 col-md-5 position-relative">
                                        <div class="winner-img-wrap h-100">
                                            <img src="https://images.unsplash.com/photo-1728921957375-877340feaf6a?q=80&w=800&auto=format&fit=crop"
                                                alt="New Market Durga Puja">
                                            <span class="winner-rank-badge badge-gold shadow-sm">
                                                <i class="bi bi-stars text-danger me-1"></i> 1st Place • Crown Champion
                                            </span>
                                            <div
                                                class="winner-img-overlay d-flex justify-content-between align-items-end">
                                                <span class="winner-vote-pill text-warning">
                                                    <i class="bi bi-fire me-1"></i> 5,537 Votes
                                                </span>
                                                <span class="badge bg-warning text-dark fw-bold">
                                                    <i class="bi bi-trophy-fill me-1"></i>GOLD TROPHY
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Column: Accolades & Winner Info -->
                                    <div
                                        class="col-12 col-md-7 d-flex flex-column justify-content-center p-3 p-md-4 position-relative overflow-hidden">
                                        <!-- Background Sunburst Halo & Sparkles -->
                                        <div class="crown-sunburst-halo" aria-hidden="true"></div>
                                        <span class="crown-sparkle sparkle-top-left" aria-hidden="true">✦</span>
                                        <span class="crown-sparkle sparkle-top-right" aria-hidden="true">✦</span>
                                        <span class="crown-sparkle sparkle-bottom-left" aria-hidden="true">✦</span>
                                        <span class="crown-sparkle sparkle-bottom-right" aria-hidden="true">✦</span>

                                        <div class="position-relative" style="z-index: 2;">
                                            <!-- Winner Header: Medallion + Title Details -->
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                <div class="winner-trophy-ring flex-shrink-0 shadow"
                                                    style="width: 56px; height: 56px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 50%, #450a0a 100%); border: 2.5px solid #fbbf24; box-shadow: 0 4px 15px rgba(185, 28, 28, 0.4);">
                                                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M5 16L3 5L8.5 10L12 4L15.5 10L21 5L19 16H5Z"
                                                            fill="url(#pinnacleRingCrown)" stroke="#78350f"
                                                            stroke-width="0.5" />
                                                        <path
                                                            d="M19 19C19 19.55 18.55 20 18 20H6C5.45 20 5 19.55 5 19V18H19V19Z"
                                                            fill="url(#pinnacleRingCrown)" stroke="#78350f"
                                                            stroke-width="0.5" />
                                                        <circle cx="3" cy="4" r="1.6" fill="#ffffff" />
                                                        <circle cx="8.5" cy="9" r="1.4" fill="#ffffff" />
                                                        <circle cx="12" cy="3" r="2.0" fill="#ffffff" />
                                                        <circle cx="15.5" cy="9" r="1.4" fill="#ffffff" />
                                                        <circle cx="21" cy="4" r="1.6" fill="#ffffff" />
                                                        <circle cx="12" cy="13.5" r="1.8" fill="#fbbf24"
                                                            stroke="#78350f" stroke-width="0.5" />
                                                        <defs>
                                                            <linearGradient id="pinnacleRingCrown" x1="0%" y1="0%"
                                                                x2="100%" y2="100%">
                                                                <stop offset="0%" stop-color="#fffbeb" />
                                                                <stop offset="35%" stop-color="#fef08a" />
                                                                <stop offset="70%" stop-color="#f59e0b" />
                                                                <stop offset="100%" stop-color="#d97706" />
                                                            </linearGradient>
                                                        </defs>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="crown-badge-pill mb-1 d-inline-flex align-items-center">
                                                        <i class="bi bi-stars text-warning me-1"></i>MAHA PURASKAR •
                                                        GRAND CHAMPION
                                                    </div>
                                                    <h4 class="fw-bold mb-1 text-dark"
                                                        style="font-size: clamp(1.25rem, 2.5vw, 1.5rem);">New Market
                                                        Durga
                                                        Puja</h4>
                                                    <p class="text-muted small mb-0">
                                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> New Market
                                                        Road,
                                                        Dibrugarh
                                                    </p>
                                                </div>
                                            </div>



                                            <!-- Accolades / Highlights Bar -->
                                            <div
                                                class="d-flex flex-wrap gap-2 pt-2 border-top border-warning border-opacity-50">
                                                <span
                                                    class="badge bg-warning bg-opacity-25 text-dark border border-warning rounded-pill px-3 py-1 small fw-semibold">
                                                    <i class="bi bi-trophy-fill text-danger me-1"></i>Maha Puraskar Gold
                                                    Trophy
                                                </span>
                                                <span
                                                    class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1 small fw-semibold">
                                                    <i class="bi bi-fire me-1"></i>5,537 Verified Votes
                                                </span>
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 small fw-semibold">
                                                    <i class="bi bi-shield-check me-1"></i>Supreme Grand Champion
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CATEGORY 1: BEST THEME WINNERS -->
                <div class="mb-5">
                    <div
                        class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 pb-2 border-bottom border-danger border-opacity-25">
                        <div class="d-flex align-items-center gap-2">

                            <h3 class="fw-bold mb-0 text-dark">Best Theme (শ্ৰেষ্ঠ থিম)</h3>
                        </div>
                        <span class="text-muted small"><i class="bi bi-star-fill text-warning me-1"></i> Concept &
                            Artistry
                            Title</span>
                    </div>

                    <div class="row g-4 justify-content-center">
                        <!-- 1st Place Gold Winner -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="winner-podium-card winner-gold h-100">
                                <span class="winner-rank-badge badge-gold">
                                    <i class="bi bi-trophy-fill me-1"></i> 1st Place • Winner
                                </span>
                                <div class="winner-img-wrap">
                                    <img src="https://images.unsplash.com/photo-1728921957375-877340feaf6a?q=80&w=800&auto=format&fit=crop"
                                        alt="New Market Durga Puja">
                                    <div class="winner-img-overlay d-flex justify-content-between align-items-end">
                                        <span class="winner-vote-pill text-warning">
                                            <i class="bi bi-fire me-1"></i> 5,537 Votes
                                        </span>
                                        <span class="badge bg-warning text-dark fw-bold">GOLD MEDAL</span>
                                    </div>
                                </div>
                                <div class="p-3 text-center">
                                    <div class="mb-2">
                                        <div class="winner-trophy-ring ring-gold mx-auto">
                                            <i class="bi bi-trophy-fill"></i>
                                        </div>
                                    </div>
                                    <h4 class="fw-bold mb-1 text-dark">New Market Durga Puja</h4>
                                    <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                        New Market Road, Dibrugarh</p>
                                    <div class="p-2 rounded bg-warning bg-opacity-10 text-dark small fw-medium">
                                        <i class="bi bi-award-fill text-warning me-1"></i> Winner of Best Theme 2026 for
                                        Exceptional Architecture & Theme Excellence.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2nd Place Silver Runner-up -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="winner-podium-card winner-silver h-100">
                                <span class="winner-rank-badge badge-silver">
                                    <i class="bi bi-award-fill me-1"></i> 2nd Place • Runner-Up
                                </span>
                                <div class="winner-img-wrap">
                                    <img src="https://images.unsplash.com/photo-1728493118702-febd0b9a0fab?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0"
                                        alt="Thana Chariali Sarbajanin">
                                    <div class="winner-img-overlay d-flex justify-content-between align-items-end">
                                        <span class="winner-vote-pill text-light">
                                            <i class="bi bi-hand-thumbs-up me-1"></i> 4,812 Votes
                                        </span>
                                        <span class="badge bg-secondary text-white fw-bold">SILVER MEDAL</span>
                                    </div>
                                </div>
                                <div class="p-3 text-center">
                                    <div class="mb-2">
                                        <div class="winner-trophy-ring ring-silver mx-auto">
                                            <i class="bi bi-award"></i>
                                        </div>
                                    </div>
                                    <h4 class="fw-bold mb-1 text-dark">Thana Chariali Sarbajanin</h4>
                                    <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                        Thana Chariali, Dibrugarh</p>
                                    <div class="p-2 rounded bg-light text-muted small fw-medium">
                                        <i class="bi bi-check-circle-fill text-secondary me-1"></i> Runner-Up for
                                        Outstanding Heritage Concept & Crowd Safety.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3rd Place Bronze Runner-up -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="winner-podium-card winner-bronze h-100">
                                <span class="winner-rank-badge badge-bronze">
                                    <i class="bi bi-shield-fill me-1"></i> 3rd Place • 2nd Runner-Up
                                </span>
                                <div class="winner-img-wrap">
                                    <img src="https://images.unsplash.com/photo-1728921957375-877340feaf6a?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0"
                                        alt="Graham Bazar Sarbajanin">
                                    <div class="winner-img-overlay d-flex justify-content-between align-items-end">
                                        <span class="winner-vote-pill text-light">
                                            <i class="bi bi-hand-thumbs-up me-1"></i> 3,940 Votes
                                        </span>
                                        <span class="badge bg-danger bg-opacity-75 text-white fw-bold">BRONZE
                                            MEDAL</span>
                                    </div>
                                </div>
                                <div class="p-3 text-center">
                                    <div class="mb-2">
                                        <div class="winner-trophy-ring ring-bronze mx-auto">
                                            <i class="bi bi-star"></i>
                                        </div>
                                    </div>
                                    <h4 class="fw-bold mb-1 text-dark">Graham Bazar Sarbajanin</h4>
                                    <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                        Graham Bazar, Dibrugarh</p>
                                    <div class="p-2 rounded bg-light text-muted small fw-medium">
                                        <i class="bi bi-check-circle-fill text-danger me-1"></i> 2nd Runner-Up for
                                        Traditional Ambiance & Community Harmony.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CATEGORY 2: BEST PRATIMA WINNERS -->
                <div class="mb-5">
                    <div
                        class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 pb-2 border-bottom border-primary border-opacity-25">
                        <div class="d-flex align-items-center gap-2">

                            <h3 class="fw-bold mb-0 text-dark">Best Pandal (শ্ৰেষ্ঠ মণ্ডপ)</h3>
                        </div>
                        <span class="text-muted small"><i class="bi bi-palette-fill me-1 text-primary"></i> Architecture
                            & Decor</span>
                    </div>

                    <div class="row g-4 justify-content-center">
                        <!-- 1st Place Gold Winner -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="winner-podium-card winner-gold h-100">
                                <span class="winner-rank-badge badge-gold">
                                    <i class="bi bi-trophy-fill me-1"></i> 1st Place • Winner
                                </span>
                                <div class="winner-img-wrap">
                                    <img src="https://images.unsplash.com/photo-1756399220348-7964d338cb19?q=80&w=1074&auto=format&fit=crop&ixlib=rb-4.1.0"
                                        alt="Kadmani Sarbajanin Pratima">
                                    <div class="winner-img-overlay d-flex justify-content-between align-items-end">
                                        <span class="winner-vote-pill text-warning">
                                            <i class="bi bi-fire me-1"></i> 4,115 Votes
                                        </span>
                                        <span class="badge bg-warning text-dark fw-bold">GOLD MEDAL</span>
                                    </div>
                                </div>
                                <div class="p-3 text-center">
                                    <div class="mb-2">
                                        <div class="winner-trophy-ring ring-gold mx-auto">
                                            <i class="bi bi-flower1"></i>
                                        </div>
                                    </div>
                                    <h4 class="fw-bold mb-1 text-dark">Kadmani Sarbajanin Pratima</h4>
                                    <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                        Kadmani, Dibrugarh</p>
                                    <div class="p-2 rounded bg-warning bg-opacity-10 text-dark small fw-medium">
                                        <i class="bi bi-award-fill text-warning me-1"></i> Crowned Best Idol for Divine
                                        Expressions & Master Clay Sculpting.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2nd Place Silver Runner-up -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="winner-podium-card winner-silver h-100">
                                <span class="winner-rank-badge badge-silver">
                                    <i class="bi bi-award-fill me-1"></i> 2nd Place • Runner-Up
                                </span>
                                <div class="winner-img-wrap">
                                    <img src="https://images.unsplash.com/photo-1728921957375-877340feaf6a?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0"
                                        alt="Jalan Nagar Sarbajanin Pratima">
                                    <div class="winner-img-overlay d-flex justify-content-between align-items-end">
                                        <span class="winner-vote-pill text-light">
                                            <i class="bi bi-hand-thumbs-up me-1"></i> 3,420 Votes
                                        </span>
                                        <span class="badge bg-secondary text-white fw-bold">SILVER MEDAL</span>
                                    </div>
                                </div>
                                <div class="p-3 text-center">
                                    <div class="mb-2">
                                        <div class="winner-trophy-ring ring-silver mx-auto">
                                            <i class="bi bi-palette"></i>
                                        </div>
                                    </div>
                                    <h4 class="fw-bold mb-1 text-dark">Jalan Nagar Sarbajanin Pratima</h4>
                                    <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                        Jalan Nagar, Dibrugarh</p>
                                    <div class="p-2 rounded bg-light text-muted small fw-medium">
                                        <i class="bi bi-check-circle-fill text-secondary me-1"></i> Runner-Up for
                                        Traditional Ornamentation & Aesthetic Finishes.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3rd Place Bronze Runner-up -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="winner-podium-card winner-bronze h-100">
                                <span class="winner-rank-badge badge-bronze">
                                    <i class="bi bi-shield-fill me-1"></i> 3rd Place • 2nd Runner-Up
                                </span>
                                <div class="winner-img-wrap">
                                    <img src="https://images.unsplash.com/photo-1728493118702-febd0b9a0fab?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0"
                                        alt="Graham Bazar Cultural Pratima">
                                    <div class="winner-img-overlay d-flex justify-content-between align-items-end">
                                        <span class="winner-vote-pill text-light">
                                            <i class="bi bi-hand-thumbs-up me-1"></i> 2,980 Votes
                                        </span>
                                        <span class="badge bg-danger bg-opacity-75 text-white fw-bold">BRONZE
                                            MEDAL</span>
                                    </div>
                                </div>
                                <div class="p-3 text-center">
                                    <div class="mb-2">
                                        <div class="winner-trophy-ring ring-bronze mx-auto">
                                            <i class="bi bi-brush"></i>
                                        </div>
                                    </div>
                                    <h4 class="fw-bold mb-1 text-dark">Graham Bazar Cultural Pratima</h4>
                                    <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                        Graham Bazar, Dibrugarh</p>
                                    <div class="p-2 rounded bg-light text-muted small fw-medium">
                                        <i class="bi bi-check-circle-fill text-danger me-1"></i> 2nd Runner-Up for
                                        Eco-Friendly Natural Clay Dyes.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CATEGORY 3: BEST LIGHTING WINNERS -->
                <div>
                    <div
                        class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 pb-2 border-bottom border-warning border-opacity-50">
                        <div class="d-flex align-items-center gap-2">

                            <h3 class="fw-bold mb-0 text-dark">Best Idol (শ্ৰেষ্ঠ প্ৰতিমা)</h3>
                        </div>
                        <span class="text-muted small"><i class="bi bi-brightness-high-fill text-warning me-1"></i>
                            Maa Durga Clay Art</span>
                    </div>

                    <div class="row g-4 justify-content-center">
                        <!-- 1st Place Gold Winner -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="winner-podium-card winner-gold h-100">
                                <span class="winner-rank-badge badge-gold">
                                    <i class="bi bi-trophy-fill me-1"></i> 1st Place • Winner
                                </span>
                                <div class="winner-img-wrap">
                                    <img src="https://images.unsplash.com/photo-1728921957375-877340feaf6a?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0"
                                        alt="Thana Chariali Illumination">
                                    <div class="winner-img-overlay d-flex justify-content-between align-items-end">
                                        <span class="winner-vote-pill text-warning">
                                            <i class="bi bi-fire me-1"></i> 5,120 Votes
                                        </span>
                                        <span class="badge bg-warning text-dark fw-bold">GOLD MEDAL</span>
                                    </div>
                                </div>
                                <div class="p-3 text-center">
                                    <div class="mb-2">
                                        <div class="winner-trophy-ring ring-gold mx-auto">
                                            <i class="bi bi-lightbulb-fill"></i>
                                        </div>
                                    </div>
                                    <h4 class="fw-bold mb-1 text-dark">Thana Chariali Sarbajanin</h4>
                                    <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                        Thana Chariali, Dibrugarh</p>
                                    <div class="p-2 rounded bg-warning bg-opacity-10 text-dark small fw-medium">
                                        <i class="bi bi-award-fill text-warning me-1"></i> Awarded Gold for 3D Dynamic
                                        LED Light Gates & Chandelier Displays.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2nd Place Silver Runner-up -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="winner-podium-card winner-silver h-100">
                                <span class="winner-rank-badge badge-silver">
                                    <i class="bi bi-award-fill me-1"></i> 2nd Place • Runner-Up
                                </span>
                                <div class="winner-img-wrap">
                                    <img src="https://images.unsplash.com/photo-1728493118702-febd0b9a0fab?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0"
                                        alt="New Market Illumination">
                                    <div class="winner-img-overlay d-flex justify-content-between align-items-end">
                                        <span class="winner-vote-pill text-light">
                                            <i class="bi bi-hand-thumbs-up me-1"></i> 4,680 Votes
                                        </span>
                                        <span class="badge bg-secondary text-white fw-bold">SILVER MEDAL</span>
                                    </div>
                                </div>
                                <div class="p-3 text-center">
                                    <div class="mb-2">
                                        <div class="winner-trophy-ring ring-silver mx-auto">
                                            <i class="bi bi-brightness-high"></i>
                                        </div>
                                    </div>
                                    <h4 class="fw-bold mb-1 text-dark">New Market Durga Puja</h4>
                                    <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                        New Market Road, Dibrugarh</p>
                                    <div class="p-2 rounded bg-light text-muted small fw-medium">
                                        <i class="bi bi-check-circle-fill text-secondary me-1"></i> Runner-Up for
                                        Synchronized Street Illumination.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3rd Place Bronze Runner-up -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="winner-podium-card winner-bronze h-100">
                                <span class="winner-rank-badge badge-bronze">
                                    <i class="bi bi-shield-fill me-1"></i> 3rd Place • 2nd Runner-Up
                                </span>
                                <div class="winner-img-wrap">
                                    <img src="https://images.unsplash.com/photo-1756399220348-7964d338cb19?q=80&w=1074&auto=format&fit=crop&ixlib=rb-4.1.0"
                                        alt="Kadmani Illumination">
                                    <div class="winner-img-overlay d-flex justify-content-between align-items-end">
                                        <span class="winner-vote-pill text-light">
                                            <i class="bi bi-hand-thumbs-up me-1"></i> 3,850 Votes
                                        </span>
                                        <span class="badge bg-danger bg-opacity-75 text-white fw-bold">BRONZE
                                            MEDAL</span>
                                    </div>
                                </div>
                                <div class="p-3 text-center">
                                    <div class="mb-2">
                                        <div class="winner-trophy-ring ring-bronze mx-auto">
                                            <i class="bi bi-sun"></i>
                                        </div>
                                    </div>
                                    <h4 class="fw-bold mb-1 text-dark">Kadmani Sarbajanin Pratima</h4>
                                    <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                        Kadmani, Dibrugarh</p>
                                    <div class="p-2 rounded bg-light text-muted small fw-medium">
                                        <i class="bi bi-check-circle-fill text-danger me-1"></i> 2nd Runner-Up for
                                        Eco-Solar Powered Illumination Arches.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>




        <section class="glimpses-section" id="gallery">

            <!-- Decorative Traditional Alpana Corner Motifs -->
            <div class="alpana-corner alpana-top-left" aria-hidden="true">
                <svg viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 0 C 50 15, 90 48, 108 98 L96 108 C 80 62, 45 28, 0 12 Z" fill="#b07b3b"
                        fill-opacity="0.38" />
                    <circle cx="56" cy="56" r="9" stroke="#800c19" stroke-width="1.8" stroke-dasharray="3 3" />
                    <path d="M14 46 C 30 40, 52 48, 58 68" stroke="#b07b3b" stroke-width="1.6" />
                    <path d="M46 14 C 40 30, 48 52, 68 58" stroke="#b07b3b" stroke-width="1.6" />
                    <circle cx="32" cy="32" r="5" fill="#800c19" fill-opacity="0.6" />
                    <path d="M10 10 L38 38 M14 70 L70 14" stroke="#b07b3b" stroke-width="1.3" stroke-linecap="round" />
                </svg>
            </div>

            <div class="alpana-corner alpana-top-right" aria-hidden="true">
                <svg viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 0 C 50 15, 90 48, 108 98 L96 108 C 80 62, 45 28, 0 12 Z" fill="#b07b3b"
                        fill-opacity="0.38" />
                    <circle cx="56" cy="56" r="9" stroke="#800c19" stroke-width="1.8" stroke-dasharray="3 3" />
                    <path d="M14 46 C 30 40, 52 48, 58 68" stroke="#b07b3b" stroke-width="1.6" />
                    <path d="M46 14 C 40 30, 48 52, 68 58" stroke="#b07b3b" stroke-width="1.6" />
                    <circle cx="32" cy="32" r="5" fill="#800c19" fill-opacity="0.6" />
                    <path d="M10 10 L38 38 M14 70 L70 14" stroke="#b07b3b" stroke-width="1.3" stroke-linecap="round" />
                </svg>
            </div>

            <div class="alpana-corner alpana-bottom-left" aria-hidden="true">
                <svg viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 0 C 50 15, 90 48, 108 98 L96 108 C 80 62, 45 28, 0 12 Z" fill="#b07b3b"
                        fill-opacity="0.3" />
                    <circle cx="56" cy="56" r="8" stroke="#800c19" stroke-width="1.6" />
                </svg>
            </div>

            <div class="alpana-corner alpana-bottom-right" aria-hidden="true">
                <svg viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 0 C 50 15, 90 48, 108 98 L96 108 C 80 62, 45 28, 0 12 Z" fill="#b07b3b"
                        fill-opacity="0.3" />
                    <circle cx="56" cy="56" r="8" stroke="#800c19" stroke-width="1.6" />
                </svg>
            </div>

            <div class="festive-bottom-trim" aria-hidden="true"></div>

            <div class="glimpses-container">

                <!-- Section Title & Subtitle -->
                <h2 class="glimpses-title">Glimpses of Puja 2026</h2>
                <p class="glimpses-subtitle">Moments, memories and magic from Dibrugarh's most loved pujas</p>

                <!-- Carousel Stage -->
                <div class="slider-stage">

                    <!-- Carousel Viewport -->
                    <div class="reels-viewport" id="reelsViewport">
                        <div class="reels-track" id="reelsTrack">

                            <!-- REEL 1: Santipara 2nd Lane Puja -->
                            <div class="reel-card" data-index="0" data-pandal="Santipara 2nd Lane Puja"
                                data-likes="14.2K" data-theme="Swarna Mandir Golden Splendor & Dhaki Rhythms"
                                data-splash="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?auto=format&fit=crop&w=700&q=80"
                                data-video="https://www.pexels.com/download/video/34945624/">
                                <video class="reel-bg"
                                    poster="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?auto=format&fit=crop&w=700&q=80"
                                    src="https://www.pexels.com/download/video/34945624/" muted loop
                                    playsinline></video>
                                <div class="reel-overlay"></div>
                                <div class="reel-play-btn" title="Watch Short">
                                    <svg viewBox="0 0 24 24">
                                        <polygon points="6,3 20,12 6,21"></polygon>
                                    </svg>
                                </div>
                                <div class="reel-caption-wrap">
                                    <p class="reel-title">Santipara 2nd Lane Puja</p>
                                </div>
                            </div>

                            <!-- REEL 2: Chowkidingee Charali Puja -->
                            <div class="reel-card" data-index="1" data-pandal="Chowkidingee Charali Puja"
                                data-likes="28.9K" data-theme="Neon Crystal Illumination & Modern Laser Art"
                                data-splash="https://images.unsplash.com/photo-1508739773434-c26b3d09e071?auto=format&fit=crop&w=700&q=80"
                                data-video="https://www.pexels.com/download/video/34945624/">
                                <video class="reel-bg"
                                    poster="https://images.unsplash.com/photo-1508739773434-c26b3d09e071?auto=format&fit=crop&w=700&q=80"
                                    src="https://www.pexels.com/download/video/34945624/" muted loop
                                    playsinline></video>
                                <div class="reel-overlay"></div>
                                <div class="reel-play-btn" title="Watch Short">
                                    <svg viewBox="0 0 24 24">
                                        <polygon points="6,3 20,12 6,21"></polygon>
                                    </svg>
                                </div>
                                <div class="reel-caption-wrap">
                                    <p class="reel-title">Chowkidingee Charali Puja</p>
                                </div>
                            </div>

                            <!-- REEL 3: Brahmaputra Nagar Puja -->
                            <div class="reel-card" data-index="2" data-pandal="Brahmaputra Nagar Puja"
                                data-likes="19.4K" data-theme="Heritage Riverfront Terracotta & Brass Bell Ambience"
                                data-splash="https://images.unsplash.com/photo-1567157577867-05ccb1388e66?auto=format&fit=crop&w=700&q=80"
                                data-video="https://www.pexels.com/download/video/34945624/">
                                <video class="reel-bg"
                                    poster="https://images.unsplash.com/photo-1567157577867-05ccb1388e66?auto=format&fit=crop&w=700&q=80"
                                    src="https://www.pexels.com/download/video/34945624/" muted loop
                                    playsinline></video>
                                <div class="reel-overlay"></div>
                                <div class="reel-play-btn" title="Watch Short">
                                    <svg viewBox="0 0 24 24">
                                        <polygon points="6,3 20,12 6,21"></polygon>
                                    </svg>
                                </div>
                                <div class="reel-caption-wrap">
                                    <p class="reel-title">Brahmaputra Nagar Puja</p>
                                </div>
                            </div>

                            <!-- REEL 4: Amolapatty Puja -->
                            <div class="reel-card" data-index="3" data-pandal="Amolapatty Puja" data-likes="22.1K"
                                data-theme="Shobeki Ekchala Daaker Saaj Pratima Aarti"
                                data-splash="https://images.unsplash.com/photo-1601662528567-526cd06f6582?auto=format&fit=crop&w=700&q=80"
                                data-video="https://www.pexels.com/download/video/34945624/">
                                <video class="reel-bg"
                                    poster="https://images.unsplash.com/photo-1601662528567-526cd06f6582?auto=format&fit=crop&w=700&q=80"
                                    src="https://www.pexels.com/download/video/34945624/" muted loop
                                    playsinline></video>
                                <div class="reel-overlay"></div>
                                <div class="reel-play-btn" title="Watch Short">
                                    <svg viewBox="0 0 24 24">
                                        <polygon points="6,3 20,12 6,21"></polygon>
                                    </svg>
                                </div>
                                <div class="reel-caption-wrap">
                                    <p class="reel-title">Amolapatty Puja</p>
                                </div>
                            </div>

                            <!-- REEL 5: Lahowal Puja -->
                            <div class="reel-card" data-index="4" data-pandal="Lahowal Puja" data-likes="11.8K"
                                data-theme="Sky Lantern Carnival & Earthen Lamp Illumination"
                                data-splash="https://images.unsplash.com/photo-1514517521153-1be72277b32f?auto=format&fit=crop&w=700&q=80"
                                data-video="https://www.pexels.com/download/video/34945624/">
                                <video class="reel-bg"
                                    poster="https://images.unsplash.com/photo-1514517521153-1be72277b32f?auto=format&fit=crop&w=700&q=80"
                                    src="https://www.pexels.com/download/video/34945624/" muted loop
                                    playsinline></video>
                                <div class="reel-overlay"></div>
                                <div class="reel-play-btn" title="Watch Short">
                                    <svg viewBox="0 0 24 24">
                                        <polygon points="6,3 20,12 6,21"></polygon>
                                    </svg>
                                </div>
                                <div class="reel-caption-wrap">
                                    <p class="reel-title">Lahowal Puja</p>
                                </div>
                            </div>

                            <!-- REEL 6: Duliajan Gate Puja -->
                            <div class="reel-card" data-index="5" data-pandal="Duliajan Gate Puja" data-likes="34.5K"
                                data-theme="Agni & Shakti Crimson Illumination Pandal"
                                data-splash="https://images.unsplash.com/photo-1576085898323-218337e3e43c?auto=format&fit=crop&w=700&q=80"
                                data-video="https://www.pexels.com/download/video/34945624/">
                                <video class="reel-bg"
                                    poster="https://images.unsplash.com/photo-1576085898323-218337e3e43c?auto=format&fit=crop&w=700&q=80"
                                    src="https://www.pexels.com/download/video/34945624/" muted loop
                                    playsinline></video>
                                <div class="reel-overlay"></div>
                                <div class="reel-play-btn" title="Watch Short">
                                    <svg viewBox="0 0 24 24">
                                        <polygon points="6,3 20,12 6,21"></polygon>
                                    </svg>
                                </div>
                                <div class="reel-caption-wrap">
                                    <p class="reel-title">Duliajan Gate Puja</p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Navigation Controls Below Viewport -->
                    <div class="reels-nav-controls">
                        <button class="nav-arrow-btn nav-arrow-prev" id="prevReelBtn" aria-label="Previous reels">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="15 18 9 12 15 6"></polyline>
                            </svg>
                        </button>
                        <button class="nav-arrow-btn nav-arrow-next" id="nextReelBtn" aria-label="Next reels">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </button>
                    </div>

                </div>


            </div>



        </section>





        <!-- Event Timeline Section (Compact) -->
        <section class="timeline-section" id="events">
            <div class="festive-pattern-bg" aria-hidden="true"></div>

            <div class="timeline-container">
                <!-- Section Title & Subtitle -->
                <h2 class="timeline-heading">Event Timeline</h2>
                <p class="timeline-subheading">Key dates for Dibrugarh Durgo Utsav 2026</p>

                <!-- Horizontal Line & Steps Wrapper -->
                <div class="timeline-track-wrapper">
                    <div class="timeline-line-base" aria-hidden="true">
                        <div class="line-ornament ornament-1"></div>
                        <div class="line-ornament ornament-2"></div>
                    </div>

                    <!-- 4 Timeline Nodes Grid -->
                    <div class="row g-0 justify-content-between align-items-start">
                        <!-- NODE 1: Voting Starts -->
                        <div class="col-6 col-md-3 timeline-step" data-milestone="Voting Starts">
                            <div class="step-circle" title="Voting Starts">
                                <svg viewBox="0 0 24 24">
                                    <path d="M11 5L6 9H2v6h4l5 4V5z" />
                                    <path d="M15.54 8.46a5 5 0 0 1 0 7.07" stroke="#6a070e" stroke-width="2"
                                        stroke-linecap="round" fill="none" />
                                </svg>
                            </div>
                            <div class="step-pin-dot"></div>
                            <h3 class="step-title">Voting Starts</h3>
                            <p class="step-date">20 Sep 2026</p>
                        </div>

                        <!-- NODE 2: Voting Ends -->
                        <div class="col-6 col-md-3 timeline-step" data-milestone="Voting Ends">
                            <div class="step-circle" title="Voting Ends">
                                <svg viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" fill="none" stroke="#6a070e"
                                        stroke-width="2" />
                                    <line x1="16" y1="2" x2="16" y2="6" stroke="#6a070e" stroke-width="2"
                                        stroke-linecap="round" />
                                    <line x1="8" y1="2" x2="8" y2="6" stroke="#6a070e" stroke-width="2"
                                        stroke-linecap="round" />
                                    <line x1="3" y1="10" x2="21" y2="10" stroke="#6a070e" stroke-width="2" />
                                </svg>
                            </div>
                            <div class="step-pin-dot"></div>
                            <h3 class="step-title">Voting Ends</h3>
                            <p class="step-date">30 Sep 2026</p>
                        </div>

                        <!-- NODE 3: Winners Announcement -->
                        <div class="col-6 col-md-3 timeline-step active-milestone"
                            data-milestone="Winners Announcement">
                            <div class="step-circle" title="Winners Announcement">
                                <svg viewBox="0 0 24 24">
                                    <path d="M6 3h12v7c0 3.31-2.69 6-6 6s-6-2.69-6-6V3z" />
                                    <line x1="12" y1="16" x2="12" y2="20" stroke="#6a070e" stroke-width="2" />
                                    <line x1="8" y1="21" x2="16" y2="21" stroke="#6a070e" stroke-width="2"
                                        stroke-linecap="round" />
                                </svg>
                            </div>
                            <div class="step-pin-dot"></div>
                            <h3 class="step-title">Winners Announcement</h3>
                            <p class="step-date">5 Oct 2026</p>
                        </div>

                        <!-- NODE 4: Award Distribution -->
                        <div class="col-6 col-md-3 timeline-step" data-milestone="Award Distribution">
                            <div class="step-circle" title="Award Distribution">
                                <svg viewBox="0 0 24 24">
                                    <polygon
                                        points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26" />
                                </svg>
                            </div>
                            <div class="step-pin-dot"></div>
                            <h3 class="step-title">Award Distribution</h3>
                            <p class="step-date">9 Oct 2026</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Terms & Conditions Section -->
        <section class="terms-conditions-section py-5 position-relative" id="terms">
            <div class="container">
                <div class="text-center mb-4">
                    <span
                        class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2 fw-bold text-uppercase mb-2"
                        style="letter-spacing: 1px; font-size: 0.75rem;">
                        <i class="bi bi-shield-check me-1"></i> Official Guidelines
                    </span>
                    <h2 class="fw-bold fs-2 text-dark mb-2">Terms &amp; Conditions</h2>
                    <p class="text-muted small mx-auto" style="max-width: 600px;">Please read the official voting rules
                        and participation guidelines for Dibrugarh Durgo Utsav 2026.</p>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="terms-list-wrapper">
                            <ul class="terms-bullet-list list-unstyled mb-0">
                                <li
                                    class="terms-bullet-item d-flex align-items-start gap-3 mb-3 border-bottom border-light">
                                    <span class="terms-check-icon text-danger flex-shrink-0 mt-1"><i
                                            class="bi bi-check-circle-fill fs-5"></i></span>
                                    <div>
                                        <strong class="text-dark">1. One Vote Per Category:</strong> Each voter is
                                        entitled to cast only 1 vote per award category (Best Puja, Best Pratima, Best
                                        Lighting) during the active voting period.
                                    </div>
                                </li>
                                <li
                                    class="terms-bullet-item d-flex align-items-start gap-3 mb-3 border-bottom border-light">
                                    <span class="terms-check-icon text-danger flex-shrink-0 mt-1"><i
                                            class="bi bi-check-circle-fill fs-5"></i></span>
                                    <div>
                                        <strong class="text-dark">2. Fair Play &amp; Anti-Spam Protection:</strong> All
                                        votes are verified in real time. Automated bot scripts, IP manipulation, or
                                        duplicate spam votes will be automatically detected and purged.
                                    </div>
                                </li>
                                <li
                                    class="terms-bullet-item d-flex align-items-start gap-3 mb-3 border-bottom border-light">
                                    <span class="terms-check-icon text-danger flex-shrink-0 mt-1"><i
                                            class="bi bi-check-circle-fill fs-5"></i></span>
                                    <div>
                                        <strong class="text-dark">3. Nominated Committee Eligibility:</strong> Only
                                        officially registered and shortlisted Sarbajanin Durga Puja committees within
                                        Dibrugarh municipality boundaries are eligible for voting.
                                    </div>
                                </li>
                                <li
                                    class="terms-bullet-item d-flex align-items-start gap-3 mb-3  border-bottom border-light">
                                    <span class="terms-check-icon text-danger flex-shrink-0 mt-1"><i
                                            class="bi bi-check-circle-fill fs-5"></i></span>
                                    <div>
                                        <strong class="text-dark">4. Jury &amp; Public Weighted Score:</strong> Winners
                                        of Dibrugarh Sharad Samman 2026 are determined by a weighted combination of
                                        verified public voting and independent jury panel evaluations.
                                    </div>
                                </li>
                                <li
                                    class="terms-bullet-item d-flex align-items-start gap-3 mb-3 border-bottom border-light">
                                    <span class="terms-check-icon text-danger flex-shrink-0 mt-1"><i
                                            class="bi bi-check-circle-fill fs-5"></i></span>
                                    <div>
                                        <strong class="text-dark">5. Safety &amp; Environmental Compliance:</strong>
                                        Participating committees must strictly adhere to eco-friendly idol guidelines,
                                        fire safety, noise limiters, and crowd management standards.
                                    </div>
                                </li>
                                <li
                                    class="terms-bullet-item d-flex align-items-start gap-3 mb-3 border-bottom border-light">
                                    <span class="terms-check-icon text-danger flex-shrink-0 mt-1"><i
                                            class="bi bi-check-circle-fill fs-5"></i></span>
                                    <div>
                                        <strong class="text-dark">6. Voting Timeline Adherence:</strong> Online voting
                                        opens on 20th September 2026 and closes on 30th September 2026 at midnight.
                                        Votes received after the cutoff will not be accepted.
                                    </div>
                                </li>
                                <li class="terms-bullet-item d-flex align-items-start gap-3">
                                    <span class="terms-check-icon text-danger flex-shrink-0 mt-1"><i
                                            class="bi bi-check-circle-fill fs-5"></i></span>
                                    <div>
                                        <strong class="text-dark">7. Final Decision Authority:</strong> The final
                                        decision of the Dibrugarh Sharad Samman 2026 Organising Committee and Jury Board
                                        is binding and non-contestable.
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Marketing Promotional Banner Section (Auto-Rotating Carousel) -->
        @include('frontend.partials.marketing-banner')
    </main>

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
                    <a href="#" class="dibrugarh-brand" title="Dibrugarh 24x7">
                        <img src="{{ vasset('event-assets/images/logo-dibru.png') }}" alt="Dibrugarh 24x7"
                            class="img-fluid footer-brand-logo">
                    </a>
                </div>

                <!-- Center: Navigation Links -->
                <div class="col-12 col-lg-6 text-center">
                    <ul class="footer-nav">
                        <li><a href="#home" class="nav-link-item active" data-page="Home">Home</a></li>
                        <li class="nav-sep">|</li>
                        <li><a href="allpujas.html" class="nav-link-item">All Pujas</a></li>
                        <li class="nav-sep">|</li>
                        <li><a href="vote.html" class="nav-link-item">Vote</a></li>
                        <li class="nav-sep">|</li>
                        <li><a href="#gallery" class="nav-link-item" data-page="Gallery">Gallery</a></li>
                        <li class="nav-sep">|</li>
                        <li><a href="#events" class="nav-link-item" data-page="Events">Events</a></li>
                        <li class="nav-sep">|</li>
                        <li><a href="#about" class="nav-link-item" data-page="About">About</a></li>
                    </ul>
                </div>

                <!-- Right: Social Media Icons -->
                <div class="col-12 col-lg-3 text-center text-lg-end">
                    <div class="footer-socials">
                        <!-- Facebook -->
                        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer"
                            class="social-btn social-facebook" title="Facebook"
                            aria-label="Visit Dibrugarh 24x7 on Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <!-- Instagram -->
                        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer"
                            class="social-btn social-instagram" title="Instagram"
                            aria-label="Visit Dibrugarh 24x7 on Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <!-- YouTube -->
                        <a href="https://youtube.com" target="_blank" rel="noopener noreferrer"
                            class="social-btn social-youtube" title="YouTube"
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

                    <!-- Left: Copyright Notice -->
                    <div class="col text-center mb-2 mb-md-0">
                        <span>&copy; 2026 Dibrugarh 24x7. All Rights Reserved.</span>
                    </div>



                </div>
            </div>
        </div>

    </footer>

    <div class="modal fade" id="shortsVideoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered shorts-modal-dialog">
            <div class="modal-content shorts-modal-content">

                <!-- 9:16 Shorts Container -->
                <div class="shorts-player-container" id="shortsPlayerContainer">

                    <!-- HTML5 Video Element with splash poster -->
                    <video id="shortsVideoElement" class="shorts-video-element d-none" loop playsinline muted>
                    </video>

                    <!-- YouTube / Embedded Video Iframe -->
                    <iframe id="shortsIframeElement" class="shorts-iframe-element"
                        src=""
                        title="Shorts Video Player"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin"
                        allowfullscreen>
                    </iframe>

                    <!-- Vignette & Shadows -->
                    <div class="shorts-vignette"></div>

                    <!-- Temporary Toast Alert -->
                    <div class="share-toast" id="shareToast"><i class="bi bi-check-circle-fill text-success me-1"></i>
                        Reel link copied!</div>

                    <!-- Center Tap Indicator -->
                    <div class="center-play-indicator" id="centerPlayIndicator">
                        <i class="bi bi-play-fill" id="indicatorIcon"></i>
                    </div>

                    <!-- Top Header -->
                    <div class="shorts-header">
                        <div class="shorts-badge">
                            <i class="bi bi-broadcast text-danger"></i>
                            <span id="shortsLocationLabel">Dibrugarh 24×7 Shorts</span>
                        </div>
                        <button type="button" class="shorts-close-btn" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <!-- Right Vertical Actions (Reels / Shorts Style) -->
                    <div class="shorts-actions">




                        <!-- Share Action -->
                        <button class="action-pill-btn" id="shortsShareBtn" title="Share Reel">
                            <div class="action-circle-icon">
                                <i class="bi bi-share-fill"></i>
                            </div>
                            <span>Share</span>
                        </button>
                    </div>

                    <!-- Bottom Information & Progress -->
                    <div class="shorts-footer">
                        <!-- Scrubbable Progress Bar -->
                        <div class="shorts-progress-bar" id="shortsProgressBar">
                            <div class="shorts-progress-fill" id="shortsProgressFill"></div>
                        </div>

                        <!-- Title & Audio Info -->
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h3 class="shorts-pandal-title" id="shortsModalPandalName">Santipara 2nd Lane Puja
                            </h3>
                            <div class="sound-wave" id="soundWaveBar">
                                <span></span><span></span><span></span><span></span>
                            </div>
                        </div>

                        <p class="shorts-theme-desc" id="shortsModalTheme">Theme: Swarna Mandir Golden Splendor
                        </p>

                        <!-- Navigation Controls (Previous / Next Short) -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="shorts-nav-arrows">
                                <button class="shorts-nav-btn" id="prevShortBtn">
                                    <i class="bi bi-chevron-up"></i> Prev
                                </button>
                                <button class="shorts-nav-btn" id="nextShortBtn">
                                    Next <i class="bi bi-chevron-down"></i>
                                </button>
                            </div>
                            <span class="text-white-50" style="font-size: 0.74rem;">Tap video to pause</span>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
    <!-- Vote Interactive Modal -->
    <div class="modal fade" id="voteModal" tabindex="-1" aria-labelledby="voteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ vasset('event-assets/images/logo-dibru.png') }}" alt="Dibrugarh 24x7 Logo"
                            class="img-fluid" style="max-height: 36px;">
                        <span class="badge bg-danger rounded-pill">Official Ballot</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body pt-3">
                    <h4 class="fw-bold mb-1" id="voteModalLabel">Cast Your Vote</h4>
                    <p class="text-muted small mb-4">Choose your favorite Pandal committee for the 2026 People's Choice
                        Award.</p>

                    <form id="voteForm">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Select Committee</label>
                            <select class="form-select" id="pandalSelect" required>
                                <option value="Thana Chariali Sarbajanin" selected>Thana Chariali Sarbajanin</option>
                                <option value="Graham Bazar Sarbajanin">Graham Bazar Sarbajanin</option>
                                <option value="New Market Durga Puja">New Market Durga Puja</option>
                                <option value="Marwari Patty Sarbajanin">Marwari Patty Sarbajanin</option>
                                <option value="Railway Colony Puja Samiti">Railway Colony Puja Samiti</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Your Mobile Number (For Verification OTP)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">+91</span>
                                <input type="tel" class="form-control" id="voterMobile"
                                    placeholder="Enter 10-digit number" maxlength="10" required pattern="[0-9]{10}">
                            </div>
                            <div class="form-text">One vote per phone number to ensure fair community polling.</div>
                        </div>

                        <!-- Already Voted Warning Message -->
                        <div id="alreadyVotedAlert" class="alert alert-danger d-none py-2 px-3 small mt-2">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            <span id="alreadyVotedMsg">This mobile number has already cast a vote in Sharad Samman 2026.
                                Only 1 vote per mobile number is permitted.</span>
                        </div>

                        <div id="voteAlert" class="alert alert-success d-none py-2 px-3 small">
                            <i class="bi bi-check-circle-fill me-1"></i> Vote recorded successfully! Thank you for
                            voting.
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-vote-pill justify-content-center py-2" id="btnSendOtp">
                                <span>Proceed to OTP Verification</span>
                                <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- OTP Verification Modal -->
    <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ vasset('event-assets/images/logo-dibru.png') }}" alt="Dibrugarh 24x7 Logo"
                            class="img-fluid" style="max-height: 34px;">
                        <span class="badge bg-warning text-dark rounded-pill fw-bold"><i
                                class="bi bi-shield-lock-fill me-1"></i> Verification</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="otpCloseBtn"></button>
                </div>

                <div class="modal-body pt-3 text-center px-4">
                    <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-circle bg-danger-subtle text-danger"
                        style="width: 56px; height: 56px; font-size: 1.5rem;">
                        <i class="bi bi-phone-vibrate-fill"></i>
                    </div>
                    <h4 class="fw-bold mb-1" id="otpModalLabel">Enter Verification Code</h4>
                    <p class="text-muted small mb-3">
                        We have sent a 4-digit OTP to <strong id="displayMobileNum" class="text-dark">+91 XXXXX
                            XXXXX</strong>
                    </p>



                    <form id="otpForm">
                        <!-- 4 Digit OTP Input Boxes -->
                        <div class="d-flex justify-content-center gap-2 my-3" id="otpInputContainer">
                            <input type="text" class="form-control text-center fs-4 fw-bold otp-digit-box" maxlength="1"
                                pattern="[0-9]" inputmode="numeric" required autocomplete="off" data-index="1">
                            <input type="text" class="form-control text-center fs-4 fw-bold otp-digit-box" maxlength="1"
                                pattern="[0-9]" inputmode="numeric" required autocomplete="off" data-index="2">
                            <input type="text" class="form-control text-center fs-4 fw-bold otp-digit-box" maxlength="1"
                                pattern="[0-9]" inputmode="numeric" required autocomplete="off" data-index="3">
                            <input type="text" class="form-control text-center fs-4 fw-bold otp-digit-box" maxlength="1"
                                pattern="[0-9]" inputmode="numeric" required autocomplete="off" data-index="4">
                        </div>

                        <!-- Invalid OTP Alert Message -->
                        <div id="otpInvalidAlert" class="alert alert-danger d-none py-2 px-3 small text-start mb-3">
                            <i class="bi bi-x-circle-fill me-1"></i> <span id="otpInvalidMsg">Invalid OTP entered!
                                Please enter the correct 4-digit code (Demo: 1234).</span>
                        </div>

                        <!-- Valid OTP Alert Message -->
                        <div id="otpValidAlert" class="alert alert-success d-none py-2 px-3 small text-start mb-3">
                            <i class="bi bi-check-circle-fill me-1"></i> <span id="otpValidMsg">OTP verified
                                successfully! Recording your vote...</span>
                        </div>

                        <!-- Vote Submitted Success Alert Message -->
                        <div id="voteSubmittedAlert" class="alert alert-success d-none py-2 px-3 small text-start mb-3">
                            <i class="bi bi-patch-check-fill me-1"></i> <span id="voteSubmittedMsg">Vote submitted
                                successfully! Thank you for participating in Sharad Samman 2026.</span>
                        </div>

                        <!-- Resend OTP Alert Toast Message -->
                        <div id="otpResendAlert" class="alert alert-warning d-none py-2 px-3 small text-start mb-3">
                            <i class="bi bi-send-check-fill me-1"></i> A new 4-digit OTP has been sent to your mobile
                            number! (Demo: 1234)
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-vote-pill justify-content-center py-2"
                                id="btnVerifyOtp">
                                <span>Verify OTP &amp; Cast Vote</span>
                                <i class="bi bi-check-lg ms-1"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-link text-muted text-decoration-none mt-1"
                                id="btnResendOtp">
                                Didn't receive OTP? <span class="text-danger fw-semibold">Resend OTP</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery 3.7.1 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ vasset('event-assets/js/script.js') }}"></script>
</body>

</html>