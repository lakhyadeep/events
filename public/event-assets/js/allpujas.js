// ==========================================================================
// ALL PUJAS DIRECTORY — DIBRUGARH DURGO UTSAV 2026
// Search with Auto-Suggestion, Live Filters & Lazy Loading
// ==========================================================================

const defaultPujasData = [
    {
        id: 1,
        name: "Thana Chariali Sarbajanin",
        location: "Thana Chariali, Central Dibrugarh",
        zone: "Central Dibrugarh",
        category: "Best Puja",
        categoryPill: "Flagship Award",
        categoryClass: "tag-best-puja",
        votes: 4812,
        established: "Est. 1948",
        theme: "Royal Vedic Mandir with Intricate Brass Relief",
        image: "https://images.unsplash.com/photo-1728921957375-877340feaf6a?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 2,
        name: "Graham Bazar Sarbajanin",
        location: "Graham Bazar, Dibrugarh",
        zone: "Graham Bazar & Station",
        category: "Best Puja",
        categoryPill: "Heritage & Splendor",
        categoryClass: "tag-best-puja",
        votes: 3940,
        established: "Est. 1952",
        theme: "Artisanal Terracotta & Clay Heritage Architecture",
        image: "https://images.unsplash.com/photo-1728493118702-febd0b9a0fab?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 3,
        name: "New Market Durga Puja",
        location: "New Market Road, Dibrugarh",
        zone: "Central Dibrugarh",
        category: "Best Puja",
        categoryPill: "1st Place Winner",
        categoryClass: "tag-best-puja",
        votes: 5537,
        established: "Est. 1961",
        theme: "Golden Palace Recreated with Traditional Assamese Motifs",
        image: "https://images.unsplash.com/photo-1728921957375-877340feaf6a?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 4,
        name: "Jalan Nagar Sarbajanin Pratima",
        location: "Jalan Nagar, Dibrugarh",
        zone: "Jalan Nagar & Outer",
        category: "Best Pratima",
        categoryPill: "Traditional Art",
        categoryClass: "tag-best-pratima",
        votes: 3420,
        established: "Est. 1970",
        theme: "Sculpted Sabeki Ekchala Durga Idol in Pristine White Sholar Saaj",
        image: "https://images.unsplash.com/photo-1728921957375-877340feaf6a?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 5,
        name: "Kadmani Sarbajanin Pratima",
        location: "Kadmani, Dibrugarh",
        zone: "Central Dibrugarh",
        category: "Best Pratima",
        categoryPill: "Traditional Art",
        categoryClass: "tag-best-pratima",
        votes: 4115,
        established: "Est. 1965",
        theme: "Lifelike Clay Eyes & Mahishasurmardini Dynamic Stance",
        image: "https://images.unsplash.com/photo-1756399220348-7964d338cb19?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 6,
        name: "Graham Bazar Cultural Pratima",
        location: "Graham Bazar, Dibrugarh",
        zone: "Graham Bazar & Station",
        category: "Best Pratima",
        categoryPill: "Traditional Art",
        categoryClass: "tag-best-pratima",
        votes: 2980,
        established: "Est. 1974",
        theme: "Natural Eco-friendly Clay Colors with Classical Draping",
        image: "https://images.unsplash.com/photo-1728493118702-febd0b9a0fab?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 7,
        name: "Marwari Patty Sarbajanin",
        location: "Marwari Patty, Dibrugarh",
        zone: "Central Dibrugarh",
        category: "Best Lighting",
        categoryPill: "Grand Illumination",
        categoryClass: "tag-best-lighting",
        votes: 4620,
        established: "Est. 1955",
        theme: "3D Dynamic LED Chandannagar Moving Light Gates",
        image: "https://images.unsplash.com/photo-1572979203492-7144e05bbf2f?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 8,
        name: "Railway Colony Puja Samiti",
        location: "Railway Colony, Dibrugarh",
        zone: "Graham Bazar & Station",
        category: "Best Lighting",
        categoryPill: "Grand Illumination",
        categoryClass: "tag-best-lighting",
        votes: 3880,
        established: "Est. 1958",
        theme: "Spectacular Synchronized Light Symphony & Canopy",
        image: "https://images.unsplash.com/photo-1598387993441-a364f854c3e1?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 9,
        name: "Shantipara Sarbajanin Durga Puja",
        location: "Shantipara 2nd Lane, Dibrugarh",
        zone: "Central Dibrugarh",
        category: "Best Puja",
        categoryPill: "Flagship Award",
        categoryClass: "tag-best-puja",
        votes: 4310,
        established: "Est. 1968",
        theme: "Swarna Mandir Golden Splendor with Dazzling Illumination",
        image: "https://images.unsplash.com/photo-1604085572504-a392ddf0d86a?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 10,
        name: "Amolapatty Sarbajanin Durga Puja",
        location: "Amolapatty, Dibrugarh",
        zone: "Heritage Old City",
        category: "Heritage Puja",
        categoryPill: "Heritage Centenary",
        categoryClass: "tag-heritage",
        votes: 3750,
        established: "Est. 1935",
        theme: "Centenary Heritage Puja with Traditional Dhak & Aarti Rites",
        image: "https://images.unsplash.com/photo-1634823136209-6aa056c0e259?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 11,
        name: "Naliapool Sarbajanin Durga Puja",
        location: "Naliapool, Dibrugarh",
        zone: "Graham Bazar & Station",
        category: "Best Lighting",
        categoryPill: "Illumination Special",
        categoryClass: "tag-best-lighting",
        votes: 3625,
        established: "Est. 1972",
        theme: "Illuminated Over-bridge Archway and Color Changing Waterfalls",
        image: "https://images.unsplash.com/photo-1605649487212-47bdab064df7?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 12,
        name: "Boiragimoth Sarbajanin Durga Puja",
        location: "Boiragimoth, Dibrugarh",
        zone: "Heritage Old City",
        category: "Eco-Friendly",
        categoryPill: "Eco-Friendly",
        categoryClass: "tag-eco",
        votes: 3210,
        established: "Est. 1976",
        theme: "100% Biodegradable Bamboo, Jute, and Paddy Husk Architecture",
        image: "https://images.unsplash.com/photo-1567157577867-05ccb1388e66?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 13,
        name: "Paltan Bazar Sarbajanin Durga Puja",
        location: "Paltan Bazar, Dibrugarh",
        zone: "Central Dibrugarh",
        category: "Best Puja",
        categoryPill: "Popular Choice",
        categoryClass: "tag-best-puja",
        votes: 3490,
        established: "Est. 1963",
        theme: "Rajasthan Fort Replicas with Handcrafted Mirror Art",
        image: "https://images.unsplash.com/photo-1728921957375-877340feaf6a?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 14,
        name: "Chiring Chapori Sarbajanin Puja",
        location: "Chiring Chapori, Dibrugarh",
        zone: "Central Dibrugarh",
        category: "Best Pratima",
        categoryPill: "Clay Artistry",
        categoryClass: "tag-best-pratima",
        votes: 3120,
        established: "Est. 1969",
        theme: "Antique Rajbari Style Traditional Bishnupur Clay Idol",
        image: "https://images.unsplash.com/photo-1756399220348-7964d338cb19?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 15,
        name: "Chowkidinghee Sarbajanin Durga Puja",
        location: "Chowkidinghee Chariali, Dibrugarh",
        zone: "Central Dibrugarh",
        category: "Best Puja",
        categoryPill: "Grand Flagship",
        categoryClass: "tag-best-puja",
        votes: 4980,
        established: "Est. 1954",
        theme: "Magnificent Lotus Temple Replica with Glowing Floral Petals",
        image: "https://images.unsplash.com/photo-1728493118702-febd0b9a0fab?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 16,
        name: "Dibrugarh Bengali Association Puja",
        location: "R.K.B. Road, Dibrugarh",
        zone: "Heritage Old City",
        category: "Heritage Puja",
        categoryPill: "Centenary Tradition",
        categoryClass: "tag-heritage",
        votes: 4230,
        established: "Est. 1928",
        theme: "Nearly Century-Old Traditional Daker Saaj & Vedic Chants",
        image: "https://images.unsplash.com/photo-1634823136209-6aa056c0e259?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 17,
        name: "Kalibari Durga Puja Samiti",
        location: "Old Kalibari, Dibrugarh",
        zone: "Heritage Old City",
        category: "Heritage Puja",
        categoryPill: "Devotional Heritage",
        categoryClass: "tag-heritage",
        votes: 3890,
        established: "Est. 1939",
        theme: "Divine Sanctum with Sandhi Puja Grand 108 Lotus Offering",
        image: "https://images.unsplash.com/photo-1601659771146-24e5efbf7708?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 18,
        name: "Police Reserve Durga Puja",
        location: "Police Reserve Lines, Dibrugarh",
        zone: "South Dibrugarh",
        category: "Best Lighting",
        categoryPill: "Illumination",
        categoryClass: "tag-best-lighting",
        votes: 3510,
        established: "Est. 1959",
        theme: "Grand Laser Light Display & Patriotic Theme Illuminations",
        image: "https://images.unsplash.com/photo-1572979203492-7144e05bbf2f?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 19,
        name: "Khalihamari Sarbajanin Durga Puja",
        location: "Khalihamari, Dibrugarh",
        zone: "Central Dibrugarh",
        category: "Best Puja",
        categoryPill: "Artistic Pandal",
        categoryClass: "tag-best-puja",
        votes: 3670,
        established: "Est. 1971",
        theme: "South Indian Gopuram Temple Carved in Eco-Friendly Wood",
        image: "https://images.unsplash.com/photo-1604085572504-a392ddf0d86a?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 20,
        name: "Milan Nagar Sarbajanin Durga Puja",
        location: "Milan Nagar J-Zone, Dibrugarh",
        zone: "Jalan Nagar & Outer",
        category: "Best Pratima",
        categoryPill: "Creative Art",
        categoryClass: "tag-best-pratima",
        votes: 3340,
        established: "Est. 1982",
        theme: "Contemporary Fusion Maa Durga Sculpted with Tribal Motifs",
        image: "https://images.unsplash.com/photo-1756399220348-7964d338cb19?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 21,
        name: "Mancotta Road Sarbajanin Puja",
        location: "Mancotta Road, Dibrugarh",
        zone: "South Dibrugarh",
        category: "Best Lighting",
        categoryPill: "Illumination Gateway",
        categoryClass: "tag-best-lighting",
        votes: 3790,
        established: "Est. 1977",
        theme: "1 Km High-Intensity LED Light Tunnel along Mancotta Avenue",
        image: "https://images.unsplash.com/photo-1605649487212-47bdab064df7?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 22,
        name: "Santipara Jubak Sangha Puja",
        location: "Santipara Main Road, Dibrugarh",
        zone: "Central Dibrugarh",
        category: "Best Puja",
        categoryPill: "Youth & Culture",
        categoryClass: "tag-best-puja",
        votes: 4120,
        established: "Est. 1980",
        theme: "Himalayan Buddhist Monastery Replica with Prayer Wheels",
        image: "https://images.unsplash.com/photo-1728921957375-877340feaf6a?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 23,
        name: "Jhapara Durga Puja Samiti",
        location: "Jhapara, Dibrugarh",
        zone: "Heritage Old City",
        category: "Heritage Puja",
        categoryPill: "Folk Traditions",
        categoryClass: "tag-heritage",
        votes: 2840,
        established: "Est. 1966",
        theme: "Assamese Bihu-Durga Harmony Folk Decor with Bell-Metal Bells",
        image: "https://images.unsplash.com/photo-1601659771146-24e5efbf7708?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 24,
        name: "AT Road Sarbajanin Durga Puja",
        location: "AT Road Commercial Hub, Dibrugarh",
        zone: "Central Dibrugarh",
        category: "Best Lighting",
        categoryPill: "Grand Illumination",
        categoryClass: "tag-best-lighting",
        votes: 4450,
        established: "Est. 1957",
        theme: "Animated Chandannagar Light Tales of Mahishasurmardini",
        image: "https://images.unsplash.com/photo-1598387993441-a364f854c3e1?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 25,
        name: "Kumaranichiga Sarbajanin Puja",
        location: "Kumaranichiga, Dibrugarh",
        zone: "South Dibrugarh",
        category: "Best Pratima",
        categoryPill: "Traditional Art",
        categoryClass: "tag-best-pratima",
        votes: 3080,
        established: "Est. 1985",
        theme: "Graceful Abhaya Mudra Idol in Traditional Golden Silk Sari",
        image: "https://images.unsplash.com/photo-1728921957375-877340feaf6a?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 26,
        name: "Maijan Tea Estate Sarbajanin Puja",
        location: "Maijan TE, Dibrugarh",
        zone: "West / Riverside",
        category: "Heritage Puja",
        categoryPill: "Tea Garden Heritage",
        categoryClass: "tag-heritage",
        votes: 3190,
        established: "Est. 1950",
        theme: "Picturesque Tea Garden Heritage with Vibrant Dholak Beats",
        image: "https://images.unsplash.com/photo-1634823136209-6aa056c0e259?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 27,
        name: "Lepetkata Durga Puja Samiti",
        location: "Lepetkata, Dibrugarh",
        zone: "South Dibrugarh",
        category: "Eco-Friendly",
        categoryPill: "Eco-Friendly",
        categoryClass: "tag-eco",
        votes: 2920,
        established: "Est. 1988",
        theme: "Recycled Cane, Bamboo Basketry & Hand-painted Earthen Pots",
        image: "https://images.unsplash.com/photo-1567157577867-05ccb1388e66?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 28,
        name: "Seujpur Sarbajanin Durga Puja",
        location: "Seujpur, Dibrugarh",
        zone: "Central Dibrugarh",
        category: "Best Puja",
        categoryPill: "Artistic Pandal",
        categoryClass: "tag-best-puja",
        votes: 3820,
        established: "Est. 1975",
        theme: "Green Biosphere Dome with Living Bonsai Plants & Terrariums",
        image: "https://images.unsplash.com/photo-1728493118702-febd0b9a0fab?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 29,
        name: "Dibru-Sukapha Sarbajanin Puja",
        location: "Nirmal Nagar, Dibrugarh",
        zone: "Jalan Nagar & Outer",
        category: "Best Pratima",
        categoryPill: "Sculptural Marvel",
        categoryClass: "tag-best-pratima",
        votes: 3260,
        established: "Est. 1983",
        theme: "Ahom Architecture Fusion Durga Idol with Brass Ornaments",
        image: "https://images.unsplash.com/photo-1756399220348-7964d338cb19?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 30,
        name: "Borbari Durga Puja Samiti",
        location: "Borbari, Dibrugarh",
        zone: "Graham Bazar & Station",
        category: "Best Lighting",
        categoryPill: "Illumination",
        categoryClass: "tag-best-lighting",
        votes: 3140,
        established: "Est. 1979",
        theme: "Kaleidoscopic Fiber-optic Constellation Dome",
        image: "https://images.unsplash.com/photo-1572979203492-7144e05bbf2f?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 31,
        name: "Tinkunia Sarbajanin Durga Puja",
        location: "Tinkunia, Dibrugarh",
        zone: "Central Dibrugarh",
        category: "Best Puja",
        categoryPill: "Grand Gateway",
        categoryClass: "tag-best-puja",
        votes: 3910,
        established: "Est. 1964",
        theme: "Victorian Clock Tower & Glass Facade Gateway",
        image: "https://images.unsplash.com/photo-1728921957375-877340feaf6a?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 32,
        name: "HS Road Sarbajanin Durga Puja",
        location: "H.S. Road, Dibrugarh",
        zone: "Central Dibrugarh",
        category: "Best Lighting",
        categoryPill: "Illumination Carnival",
        categoryClass: "tag-best-lighting",
        votes: 4210,
        established: "Est. 1960",
        theme: "Musical Fountain with Synchronized Illumination Show",
        image: "https://images.unsplash.com/photo-1605649487212-47bdab064df7?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 33,
        name: "Gabharu Pather Sarbajanin Puja",
        location: "Gabharu Pather, Dibrugarh",
        zone: "South Dibrugarh",
        category: "Eco-Friendly",
        categoryPill: "Eco-Friendly",
        categoryClass: "tag-eco",
        votes: 2780,
        established: "Est. 1991",
        theme: "Handloom Weavers Village Theme with Sualkuchi Silk Fabrics",
        image: "https://images.unsplash.com/photo-1567157577867-05ccb1388e66?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 34,
        name: "Convoy Road Sarbajanin Puja",
        location: "Convoy Road, Dibrugarh",
        zone: "Central Dibrugarh",
        category: "Best Pratima",
        categoryPill: "Traditional Art",
        categoryClass: "tag-best-pratima",
        votes: 3380,
        established: "Est. 1981",
        theme: "Serene Mahisasuramardini with 10 Weapon Details in Fine Silver",
        image: "https://images.unsplash.com/photo-1756399220348-7964d338cb19?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 35,
        name: "Mohanaghat Sarbajanin Puja",
        location: "Mohanaghat Brahmaputra Bank, Dibrugarh",
        zone: "West / Riverside",
        category: "Heritage Puja",
        categoryPill: "Riverside Heritage",
        categoryClass: "tag-heritage",
        votes: 3560,
        established: "Est. 1953",
        theme: "Brahmaputra Wave Pavilion with Evening Ganga-Style Aarti",
        image: "https://images.unsplash.com/photo-1601659771146-24e5efbf7708?q=80&w=800&auto=format&fit=crop"
    },
    {
        id: 36,
        name: "Rehabari Sarbajanin Durga Puja",
        location: "Rehabari, Dibrugarh",
        zone: "Graham Bazar & Station",
        category: "Best Puja",
        categoryPill: "Cultural Splendor",
        categoryClass: "tag-best-puja",
        votes: 3690,
        established: "Est. 1973",
        theme: "Terracotta Relief Panels Celebrating Assam's Riverine Culture",
        image: "https://images.unsplash.com/photo-1728493118702-febd0b9a0fab?q=80&w=800&auto=format&fit=crop"
    }
];

// Configuration for Lazy Loading & Pagination
const INITIAL_BATCH = 12;
const BATCH_SIZE = 6;

// Dynamic Dataset Resolution
const allPujasData = (typeof window !== 'undefined' && Array.isArray(window.dynamicPujasData))
    ? window.dynamicPujasData
    : (typeof window !== 'undefined' && window.dynamicPujasData === undefined ? defaultPujasData : []);

let currentZone = "all";
let currentSearchQuery = "";
let currentSort = "popular";
let currentDisplayedCount = 0;
let filteredPujas = [];
let isLoadingMore = false;
let activeSuggestIndex = -1;
let currentSuggestions = [];

// Track voted numbers and active vote target
let votedMobileNumbers = new Set(['9876543210', '9999999999']);
let pendingVoteData = { mobile: '', pandal: '', pujaId: null };

$(document).ready(function () {

    // 0. Update chip counts dynamically based on allPujasData
    updateChipCounts();

    // 1. Populate Modal Ballot Select with all registered Pujas
    populateBallotOptions();

    // 2. Initial Filter and Card Render
    applyFiltersAndSort();

    // 3. Setup Search Field & Auto-Suggestion
    setupSearchAndAutoSuggest();

    // 4. Setup Category Filter Pills
    setupCategoryPills();

    // 5. Setup Sort Dropdown
    $('#pujasSortSelect').on('change', function () {
        currentSort = $(this).val();
        applyFiltersAndSort();
    });

    // 6. Setup Lazy Loading via Intersection Observer & Load More Button
    setupLazyLoading();

    // 7. Setup Voting Modal & OTP Handlers
    setupVotingHandlers();

});

// ==========================================================================
// 1. POPULATE BALLOT SELECT OPTIONS
// ==========================================================================
function populateBallotOptions() {
    const $pandalSelect = $('#pandalSelect');
    if (!$pandalSelect.length) return;

    $pandalSelect.empty();
    allPujasData.forEach((puja, index) => {
        const isSelected = index === 0 ? 'selected' : '';
        $pandalSelect.append(`<option value="${escapeHtml(puja.name)}" ${isSelected}>${escapeHtml(puja.name)} (${escapeHtml(puja.location)})</option>`);
    });
}

function updateChipCounts() {
    $('.filter-chip-btn').each(function () {
        const zone = $(this).attr('data-zone');
        let count = 0;
        if (zone === 'all') {
            count = allPujasData.length;
        } else if (zone === 'Outer & Riverside') {
            count = allPujasData.filter(p => p.zone === 'Jalan Nagar & Outer' || p.zone === 'West / Riverside' || p.zone === 'Outer & Riverside').length;
        } else {
            count = allPujasData.filter(p => p.zone === zone).length;
        }
        $(this).find('.chip-count').text(count);
    });
}

// ==========================================================================
// 2. FILTER & SORT LOGIC
// ==========================================================================
function applyFiltersAndSort() {
    let result = [...allPujasData];

    // Filter by Zone / Location
    if (currentZone !== "all") {
        if (currentZone === "Outer & Riverside") {
            result = result.filter(p => p.zone === "Jalan Nagar & Outer" || p.zone === "West / Riverside");
        } else {
            result = result.filter(p => p.zone === currentZone);
        }
    }

    // Filter by Search Query
    if (currentSearchQuery.trim() !== "") {
        const query = currentSearchQuery.toLowerCase().trim();
        result = result.filter(p => {
            return p.name.toLowerCase().includes(query) ||
                p.location.toLowerCase().includes(query) ||
                p.zone.toLowerCase().includes(query) ||
                p.category.toLowerCase().includes(query) ||
                p.theme.toLowerCase().includes(query);
        });
    }

    // Sorting
    if (currentSort === "popular") {
        result.sort((a, b) => b.votes - a.votes);
    } else if (currentSort === "name-asc") {
        result.sort((a, b) => a.name.localeCompare(b.name));
    } else if (currentSort === "name-desc") {
        result.sort((a, b) => b.name.localeCompare(a.name));
    }

    filteredPujas = result;
    currentDisplayedCount = 0;

    // Reset Container & Render Initial Batch
    $('#pujasCardsGrid').empty();
    renderNextBatch(INITIAL_BATCH);
    updateResultCounters();
}

// ==========================================================================
// 3. RENDER CARDS BATCH (LAZY LOAD)
// ==========================================================================
function renderNextBatch(countToLoad) {
    const $grid = $('#pujasCardsGrid');
    const total = filteredPujas.length;

    if (total === 0) {
        $grid.html(`
            <div class="col-12">
                <div class="empty-pujas-box shadow-sm">
                    <div class="empty-icon-circle">
                        <i class="bi bi-search"></i>
                    </div>
                    <h3 class="fw-bold mb-2 text-dark">No Pujas Found</h3>
                    <p class="text-muted mb-4">No registered pandals match "<strong>${escapeHtml(currentSearchQuery)}</strong>". Try searching with another location or clearing filters.</p>
                    <button class="btn btn-outline-danger rounded-pill px-4" id="btnResetSearch">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Clear Search &amp; Filters
                    </button>
                </div>
            </div>
        `);
        $('#lazyLoadTriggerSection').hide();
        return;
    }

    const startIndex = currentDisplayedCount;
    const endIndex = Math.min(startIndex + countToLoad, total);

    for (let i = startIndex; i < endIndex; i++) {
        const puja = filteredPujas[i];
        const cardHtml = `
            <div class="col-12 col-md-6 col-lg-4 card-appear-animate" id="puja-card-${puja.id}" data-pandal="${escapeHtml(puja.name)}" data-id="${puja.id}">
                <div class="vote-card h-100 p-2 p-md-3 d-flex flex-column">
                    <div class="position-relative mb-3">
                        <span class="card-num-badge">#${String(puja.id).padStart(2, '0')}</span>
                        <img src="${puja.image}"
                             alt="${escapeHtml(puja.name)}"
                             class="img-fluid rounded-3 w-100"
                             style="height: 200px; object-fit: cover;"
                             loading="lazy"
                             onerror="this.onerror=null;this.src='images/hero.png';">
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-baseline mb-1">
                        <h5 class="fw-bold mb-0 text-dark fs-6 fs-md-5">${escapeHtml(puja.name)}</h5>
                    </div>

                    <p class="text-muted small mb-2">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> ${escapeHtml(puja.location)}
                    </p>

                    <div class="small text-secondary mb-3 fst-italic" style="font-size: 0.82rem; line-height: 1.4;">
                        <i class="bi bi-stars text-warning me-1"></i> Theme: "${escapeHtml(puja.theme)}"
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-auto">
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.72rem;">Current Count</small>
                            <span class="fw-bold fs-6 text-danger" id="count-puja-${puja.id}">${puja.votes.toLocaleString()} votes</span>
                        </div>
                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3 vote-trigger-btn"
                            data-pandal="${escapeHtml(puja.name)}" data-puja-id="${puja.id}">
                            <i class="bi bi-hand-thumbs-up me-1"></i> Vote
                        </button>
                    </div>
                </div>
            </div>
        `;
        $grid.append(cardHtml);
    }

    currentDisplayedCount = endIndex;
    updateResultCounters();

    // Check if all cards have loaded
    if (currentDisplayedCount >= total) {
        $('#lazySpinner').hide();
        $('#btnLoadMore').hide();
        $('#allLoadedIndicator').show();
    } else {
        $('#lazySpinner').hide();
        $('#btnLoadMore').show();
        $('#allLoadedIndicator').hide();
    }

    $('#lazyLoadTriggerSection').show();
}

function updateResultCounters() {
    const total = filteredPujas.length;
    $('#resultCountBadge').text(`Showing ${currentDisplayedCount} of ${total} Pujas`);
}

// ==========================================================================
// 4. SEARCH & AUTO-SUGGESTION SYSTEM
// ==========================================================================
function setupSearchAndAutoSuggest() {
    const $input = $('#pujaSearchInput');
    const $clearBtn = $('#searchClearBtn');
    const $dropdown = $('#searchSuggestionsDropdown');
    const $list = $('#suggestionsList');

    // On user typing in search input
    $input.on('input focus', function () {
        const query = $(this).val();
        currentSearchQuery = query;

        if (query.trim().length > 0) {
            $clearBtn.show();
            generateSuggestions(query);
        } else {
            $clearBtn.hide();
            $dropdown.hide();
        }

        // Live grid update
        applyFiltersAndSort();
    });

    // Clear Button Click
    $clearBtn.on('click', function () {
        $input.val('').focus();
        currentSearchQuery = '';
        $clearBtn.hide();
        $dropdown.hide();
        applyFiltersAndSort();
    });

    // Reset from Empty State
    $(document).on('click', '#btnResetSearch', function () {
        $input.val('');
        currentSearchQuery = '';
        currentZone = 'all';
        $('.filter-chip-btn').removeClass('active');
        $('.filter-chip-btn[data-zone="all"]').addClass('active');
        $clearBtn.hide();
        $dropdown.hide();
        applyFiltersAndSort();
    });

    // Keyboard Navigation for Auto Suggest
    $input.on('keydown', function (e) {
        if (!$dropdown.is(':visible') || currentSuggestions.length === 0) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $dropdown.hide();
            }
            return;
        }

        const items = $list.find('.autosuggest-item');

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeSuggestIndex = (activeSuggestIndex + 1) % items.length;
            updateActiveSuggestion(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeSuggestIndex = (activeSuggestIndex - 1 + items.length) % items.length;
            updateActiveSuggestion(items);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeSuggestIndex >= 0 && activeSuggestIndex < items.length) {
                items.eq(activeSuggestIndex).trigger('click');
            } else {
                $dropdown.hide();
            }
        } else if (e.key === 'Escape') {
            $dropdown.hide();
        }
    });

    // Hide dropdown on click outside
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.search-box-wrapper').length) {
            $dropdown.hide();
        }
    });
}

function updateActiveSuggestion(items) {
    items.removeClass('active');
    if (activeSuggestIndex >= 0) {
        const $active = items.eq(activeSuggestIndex);
        $active.addClass('active');
        // Scroll into view if needed
        const container = document.getElementById('searchSuggestionsDropdown');
        if (container) {
            const activeEl = $active[0];
            const top = activeEl.offsetTop - container.offsetTop;
            if (top < container.scrollTop || top + activeEl.clientHeight > container.scrollTop + container.clientHeight) {
                container.scrollTop = top;
            }
        }
    }
}

function generateSuggestions(query) {
    const q = query.toLowerCase().trim();
    const $dropdown = $('#searchSuggestionsDropdown');
    const $list = $('#suggestionsList');
    activeSuggestIndex = -1;

    // Find matches
    currentSuggestions = allPujasData.filter(p => {
        return p.name.toLowerCase().includes(q) ||
            p.location.toLowerCase().includes(q) ||
            p.zone.toLowerCase().includes(q) ||
            p.theme.toLowerCase().includes(q);
    }).slice(0, 7); // Show top 7 suggestions

    if (currentSuggestions.length === 0) {
        $list.html(`
            <li class="p-3 text-center text-muted small">
                <i class="bi bi-info-circle me-1"></i> No matching suggestions for "<strong>${escapeHtml(query)}</strong>"
            </li>
        `);
        $('#suggestionsHeaderCount').text('0 matches');
        $dropdown.show();
        return;
    }

    $('#suggestionsHeaderCount').text(`${currentSuggestions.length} suggestions`);
    let html = '';

    currentSuggestions.forEach(p => {
        const highlightedName = highlightMatch(p.name, query);
        const highlightedLoc = highlightMatch(p.location, query);

        html += `
            <li class="autosuggest-item" data-id="${p.id}" data-name="${escapeHtml(p.name)}">
                <img src="${p.image}" alt="${escapeHtml(p.name)}" class="autosuggest-thumb" onerror="this.src='images/hero.png';">
                <div class="autosuggest-info">
                    <div class="autosuggest-title">${highlightedName}</div>
                    <div class="autosuggest-meta">
                        <span><i class="bi bi-geo-alt-fill text-danger"></i> ${highlightedLoc}</span>
                        <span class="badge bg-light text-dark border autosuggest-pill">${escapeHtml(p.zone)}</span>
                        <span class="text-danger fw-semibold"><i class="bi bi-hand-thumbs-up-fill me-1"></i>${p.votes.toLocaleString()} votes</span>
                    </div>
                </div>
            </li>
        `;
    });

    $list.html(html);
    $dropdown.show();

    // Click handler on auto-suggestion item
    $list.find('.autosuggest-item').on('click', function () {
        const pujaName = $(this).attr('data-name');
        const pujaId = $(this).attr('data-id');

        $('#pujaSearchInput').val(pujaName);
        currentSearchQuery = pujaName;
        $('#searchClearBtn').show();
        $dropdown.hide();

        applyFiltersAndSort();

        // Smooth scroll and pulse highlight on target card
        setTimeout(function () {
            const $targetCard = $(`#puja-card-${pujaId}`);
            if ($targetCard.length) {
                $('html, body').animate({
                    scrollTop: $targetCard.offset().top - 120
                }, 500);
                $targetCard.find('.vote-card').addClass('card-flash-highlight');
                setTimeout(() => {
                    $targetCard.find('.vote-card').removeClass('card-flash-highlight');
                }, 2400);
            }
        }, 150);
    });
}

function highlightMatch(text, query) {
    if (!query) return escapeHtml(text);
    const escapedQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const regex = new RegExp(`(${escapedQuery})`, 'gi');
    return escapeHtml(text).replace(regex, '<mark class="autosuggest-highlight">$1</mark>');
}

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// ==========================================================================
// 5. CATEGORY FILTER PILLS
// ==========================================================================
function setupCategoryPills() {
    $('.filter-chip-btn').on('click', function () {
        $('.filter-chip-btn').removeClass('active');
        $(this).addClass('active');

        currentZone = $(this).attr('data-zone') || 'all';
        applyFiltersAndSort();
    });
}

// ==========================================================================
// 6. LAZY LOADING SYSTEM
// ==========================================================================
function setupLazyLoading() {
    // "Load More Pujas" button click
    $('#btnLoadMore').on('click', function () {
        triggerLazyBatch();
    });

    // IntersectionObserver Sentinel for Infinite Scroll Lazy Load
    const sentinel = document.getElementById('lazyLoadSentinel');
    if (sentinel && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !isLoadingMore && currentDisplayedCount < filteredPujas.length) {
                    triggerLazyBatch();
                }
            });
        }, {
            rootMargin: '200px 0px',
            threshold: 0.1
        });

        observer.observe(sentinel);
    }
}

function triggerLazyBatch() {
    if (isLoadingMore || currentDisplayedCount >= filteredPujas.length) return;

    isLoadingMore = true;
    $('#lazySpinner').show();
    $('#btnLoadMore').hide();

    // Subtle 350ms simulation for smooth progressive loading
    setTimeout(() => {
        renderNextBatch(BATCH_SIZE);
        isLoadingMore = false;
    }, 350);
}

// ==========================================================================
// 7. VOTING & OTP VERIFICATION FLOW
// ==========================================================================
function setupVotingHandlers() {

    // Trigger Vote Modal from any Card (event delegation)
    $(document).on('click', '.vote-trigger-btn', function () {
        const pandalName = $(this).attr('data-pandal');
        const pujaId = $(this).attr('data-puja-id');

        pendingVoteData.pandal = pandalName;
        pendingVoteData.pujaId = pujaId;

        $('#pandalSelect').val(pandalName);
        $('#alreadyVotedAlert').addClass('d-none');

        const voteModal = new bootstrap.Modal(document.getElementById('voteModal'));
        voteModal.show();
    });

    // Step 1: Submit Mobile Number to trigger OTP or show Already Voted alert
    $('#voteForm').on('submit', function (e) {
        e.preventDefault();
        $('#alreadyVotedAlert').addClass('d-none');

        const mobile = $('#voterMobile').val().trim();
        const pandal = $('#pandalSelect').val();

        if (mobile.length !== 10 || !/^\d{10}$/.test(mobile)) {
            alert('Please enter a valid 10-digit mobile number.');
            return;
        }

        // Check if Mobile Number has already voted
        if (votedMobileNumbers.has(mobile)) {
            $('#alreadyVotedMsg').html('Mobile number <strong>+91 ' + mobile + '</strong> has already cast a vote in Sharad Samman 2026. Only 1 vote per phone number is permitted.');
            $('#alreadyVotedAlert').removeClass('d-none').hide().fadeIn(300);
            return;
        }

        pendingVoteData.mobile = mobile;
        pendingVoteData.pandal = pandal;

        // Hide Vote Modal & Show OTP Modal
        const voteModalEl = document.getElementById('voteModal');
        const voteModalInstance = bootstrap.Modal.getInstance(voteModalEl);
        if (voteModalInstance) {
            voteModalInstance.hide();
        }

        // Prepare OTP Modal
        $('#displayMobileNum').text('+91 ' + mobile);
        $('.otp-digit-box').val('').removeClass('is-invalid');
        $('#otpInvalidAlert, #otpValidAlert, #voteSubmittedAlert, #otpResendAlert').addClass('d-none');
        $('#btnVerifyOtp').prop('disabled', false).html('<span>Verify OTP &amp; Cast Vote</span> <i class="bi bi-check-lg ms-1"></i>');

        setTimeout(function () {
            const otpModal = new bootstrap.Modal(document.getElementById('otpModal'));
            otpModal.show();
            setTimeout(function () {
                $('.otp-digit-box[data-index="1"]').focus();
            }, 300);
        }, 300);
    });

    // OTP Input Boxes Auto-Tab & Backspace Logic
    $('.otp-digit-box').on('input keyup', function (e) {
        const $this = $(this);
        const val = $this.val();
        const index = parseInt($this.attr('data-index'));

        $('.otp-digit-box').removeClass('is-invalid');
        $('#otpInvalidAlert').addClass('d-none');

        if (val.length === 1 && index < 4 && e.key !== 'Backspace') {
            $('.otp-digit-box[data-index="' + (index + 1) + '"]').focus();
        }

        if (e.key === 'Backspace' && val.length === 0 && index > 1) {
            $('.otp-digit-box[data-index="' + (index - 1) + '"]').focus();
        }
    });

    // Handle Paste into OTP Input
    $('.otp-digit-box').on('paste', function (e) {
        e.preventDefault();
        const pasteData = (e.originalEvent.clipboardData || window.clipboardData).getData('text').trim();
        if (/^\d{4}$/.test(pasteData)) {
            const digits = pasteData.split('');
            $('.otp-digit-box').each(function (idx) {
                $(this).val(digits[idx]).removeClass('is-invalid');
            });
            $('.otp-digit-box[data-index="4"]').focus();
        }
    });

    // Step 2: Submit OTP Form & Verification Handler
    $('#otpForm').on('submit', function (e) {
        e.preventDefault();

        let enteredOtp = '';
        $('.otp-digit-box').each(function () {
            enteredOtp += $(this).val().trim();
        });

        $('#otpInvalidAlert, #otpValidAlert, #voteSubmittedAlert, #otpResendAlert').addClass('d-none');

        // Check Demo OTP: 1234 is VALID
        if (enteredOtp === '1234') {
            $('#otpValidAlert').removeClass('d-none').hide().fadeIn(200);
            $('#btnVerifyOtp').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Verifying &amp; Recording Vote...');

            setTimeout(function () {
                // Register mobile number as already voted
                if (pendingVoteData.mobile) {
                    votedMobileNumbers.add(pendingVoteData.mobile);
                }

                // Increment vote count in dataset & in DOM
                const pandalName = pendingVoteData.pandal;
                const matchedPuja = allPujasData.find(p => p.name === pandalName);
                if (matchedPuja) {
                    matchedPuja.votes += 1;
                    $(`#count-puja-${matchedPuja.id}`).text(matchedPuja.votes.toLocaleString() + ' votes');
                }

                $('#otpValidAlert').addClass('d-none');
                $('#voteSubmittedAlert').removeClass('d-none').hide().fadeIn(200);
                $('#btnVerifyOtp').html('<i class="bi bi-check-circle-fill me-1"></i> Vote Submitted Successfully!');

                setTimeout(function () {
                    const otpModalEl = document.getElementById('otpModal');
                    const otpModalInstance = bootstrap.Modal.getInstance(otpModalEl);
                    if (otpModalInstance) {
                        otpModalInstance.hide();
                    }
                    $('#voteForm')[0].reset();
                    $('#otpForm')[0].reset();
                }, 1600);

            }, 900);

        } else {
            $('.otp-digit-box').addClass('is-invalid');
            $('#otpInvalidAlert').removeClass('d-none').hide().fadeIn(200);
            setTimeout(function () {
                $('.otp-digit-box[data-index="1"]').focus().select();
            }, 300);
        }
    });

    // Resend OTP Button Click
    $('#btnResendOtp').on('click', function () {
        $('.otp-digit-box').val('').removeClass('is-invalid');
        $('#otpInvalidAlert, #otpValidAlert, #voteSubmittedAlert').addClass('d-none');
        $('#otpResendAlert').removeClass('d-none').hide().fadeIn(200).delay(3500).fadeOut(300);
        $('.otp-digit-box[data-index="1"]').focus();
    });

    // Navbar Elevation on Scroll
    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 30) {
            $('.header-wrapper').css({
                'box-shadow': '0 8px 24px rgba(0, 0, 0, 0.08)',
                'background-color': '#fffcf7'
            });
        } else {
            $('.header-wrapper').css({
                'box-shadow': '0 4px 20px rgba(0, 0, 0, 0.04)',
                'background-color': '#fdf8f2'
            });
        }
    });
}
