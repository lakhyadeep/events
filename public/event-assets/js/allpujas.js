// ==========================================================================
// ALL PUJAS DIRECTORY — DIBRUGARH DURGO UTSAV 2026
// Search with Auto-Suggestion, Live Filters & Lazy Loading
// ==========================================================================

// Dynamic Dataset populated from database via Blade template
const allPujasData = (window.dynamicPujasData && Array.isArray(window.dynamicPujasData)) ? window.dynamicPujasData : [];

// Configuration for Lazy Loading & Pagination
const INITIAL_BATCH = 12;
const BATCH_SIZE = 6;

let currentZone = "all";
let currentSearchQuery = "";
let currentSort = "popular";
let currentDisplayedCount = 0;
let filteredPujas = [];
let isLoadingMore = false;
let activeSuggestIndex = -1;
let currentSuggestions = [];

// Track active vote target
let pendingVoteData = { mobile: '', pandal: '', pujaId: null, awardId: null, eventId: null };

$(document).ready(function () {

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
    if (allPujasData.length === 0) {
        $pandalSelect.append('<option value="" disabled selected>No committees available for voting</option>');
        return;
    }
    allPujasData.forEach((puja, index) => {
        const isSelected = index === 0 ? 'selected' : '';
        const loc = puja.location || puja.zone || 'Dibrugarh';
        $pandalSelect.append(`<option value="${puja.id}" data-name="${escapeHtml(puja.name)}" ${isSelected}>${escapeHtml(puja.name)} (${escapeHtml(loc)})</option>`);
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
                             onerror="this.onerror=null;this.src='/event-assets/images/hero.png';">
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
                        ${(typeof window.isVotingOpen === "undefined" || window.isVotingOpen) ? `
                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3 vote-trigger-btn"
                            data-pandal="${escapeHtml(puja.name)}" data-puja-id="${puja.id}" data-participant-id="${puja.id}">
                            <i class="bi bi-hand-thumbs-up me-1"></i> Vote
                        </button>` : `
                        <button class="btn btn-sm btn-secondary rounded-pill px-3 opacity-75" disabled title="Voting Closed">
                            <i class="bi bi-lock-fill me-1"></i> Closed
                        </button>`}
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
        const pujaId = $(this).attr('data-participant-id') || $(this).attr('data-puja-id');
        const awardId = $(this).attr('data-award-id') || '';

        pendingVoteData.pandal = pandalName;
        pendingVoteData.pujaId = pujaId;
        pendingVoteData.awardId = awardId;

        if (pujaId) {
            $('#pandalSelect').val(pujaId);
        } else if (pandalName) {
            const $opt = $(`#pandalSelect option[data-name="${pandalName}"]`);
            if ($opt.length) {
                $('#pandalSelect').val($opt.val());
            }
        }

        if (awardId) {
            $('#awardSelect').val(awardId);
        }

        $('#alreadyVotedAlert').addClass('d-none');

        const voteModal = new bootstrap.Modal(document.getElementById('voteModal'));
        voteModal.show();
    });

    // Step 1: Submit Mobile Number to trigger real OTP or show Already Voted alert
    $('#voteForm').on('submit', function (e) {
        e.preventDefault();
        $('#alreadyVotedAlert').addClass('d-none');

        const mobile = $('#voterMobile').val().trim();
        const participantId = $('#pandalSelect').val();
        const awardId = $('#awardSelect').val() || null;
        const eventId = $('#voteEventId').val() || (window.currentEventId || null);

        if (mobile.length !== 10 || !/^\d{10}$/.test(mobile)) {
            alert('Please enter a valid 10-digit mobile number.');
            return;
        }

        if (!participantId) {
            alert('Please select a committee to vote for.');
            return;
        }

        pendingVoteData.mobile = mobile;
        pendingVoteData.pujaId = participantId;
        pendingVoteData.awardId = awardId;
        pendingVoteData.eventId = eventId;

        const $btn = $('#btnSendOtp');
        const originalBtnHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Checking &amp; Sending OTP...');

        $.ajax({
            url: (window.apiBaseUrl || '/api/v1') + '/otp/send',
            type: 'POST',
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            data: JSON.stringify({
                phone: mobile,
                purpose: 'voting',
                event_id: eventId ? parseInt(eventId) : null,
                participant_id: parseInt(participantId),
                award_id: awardId ? parseInt(awardId) : null
            }),
            success: function () {
                $btn.prop('disabled', false).html(originalBtnHtml);

                const voteModalEl = document.getElementById('voteModal');
                const voteModalInstance = bootstrap.Modal.getInstance(voteModalEl);
                if (voteModalInstance) {
                    voteModalInstance.hide();
                }

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
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html(originalBtnHtml);
                const errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to proceed with vote. Please try again.';
                $('#alreadyVotedMsg').html(errMsg);
                $('#alreadyVotedAlert').removeClass('d-none').hide().fadeIn(300);
            }
        });
    });

    // Dynamic OTP Input Boxes Auto-Tab & Backspace Logic
    const totalOtpDigits = $('.otp-digit-box').length || 6;
    $('.otp-digit-box').on('input keyup', function (e) {
        const $this = $(this);
        const val = $this.val();
        const index = parseInt($this.attr('data-index'));

        $('.otp-digit-box').removeClass('is-invalid');
        $('#otpInvalidAlert').addClass('d-none');

        if (val.length === 1 && index < totalOtpDigits && e.key !== 'Backspace') {
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
        const digits = pasteData.replace(/\D/g, '').split('');
        if (digits.length > 0) {
            $('.otp-digit-box').each(function (idx) {
                if (digits[idx]) {
                    $(this).val(digits[idx]).removeClass('is-invalid');
                }
            });
            const focusIdx = Math.min(digits.length, totalOtpDigits);
            $('.otp-digit-box[data-index="' + focusIdx + '"]').focus();
        }
    });

    // Step 2: Submit OTP Form & Real Verification Handler
    $('#otpForm').on('submit', function (e) {
        e.preventDefault();

        let enteredOtp = '';
        $('.otp-digit-box').each(function () {
            enteredOtp += $(this).val().trim();
        });

        $('#otpInvalidAlert, #otpValidAlert, #voteSubmittedAlert, #otpResendAlert').addClass('d-none');

        if (enteredOtp.length < 4) {
            $('.otp-digit-box').addClass('is-invalid');
            $('#otpInvalidMsg').text('Please enter the complete verification code.');
            $('#otpInvalidAlert').removeClass('d-none').hide().fadeIn(200);
            return;
        }

        const $verifyBtn = $('#btnVerifyOtp');
        $verifyBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Verifying &amp; Recording Vote...');

        $.ajax({
            url: (window.apiBaseUrl || '/api/v1') + '/votes/cast',
            type: 'POST',
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            data: JSON.stringify({
                event_id: pendingVoteData.eventId ? parseInt(pendingVoteData.eventId) : parseInt($('#voteEventId').val()),
                participant_id: parseInt(pendingVoteData.pujaId),
                award_id: pendingVoteData.awardId ? parseInt(pendingVoteData.awardId) : null,
                phone: pendingVoteData.mobile,
                otp: enteredOtp
            }),
            success: function (res) {
                $('#otpValidAlert').removeClass('d-none').hide().fadeIn(200);

                // Increment vote count in dataset & in DOM
                const participantId = pendingVoteData.pujaId;
                const matchedPuja = allPujasData.find(p => String(p.id) === String(participantId));
                if (matchedPuja) {
                    matchedPuja.votes = (matchedPuja.votes || 0) + 1;
                    $(`#count-puja-${matchedPuja.id}`).text(matchedPuja.votes.toLocaleString() + ' votes');
                }

                $(`.live-vote-count-${participantId}`).each(function () {
                    const current = parseInt($(this).text().replace(/[^0-9]/g, '')) || 0;
                    $(this).html('<i class="bi bi-hand-thumbs-up-fill me-1"></i>' + (current + 1).toLocaleString());
                });

                const $totalCounter = $('#liveTotalCounter');
                if ($totalCounter.length) {
                    const currentTotal = parseInt($totalCounter.text().replace(/,/g, '')) || 0;
                    $totalCounter.text((currentTotal + 1).toLocaleString());
                }

                setTimeout(function () {
                    $('#otpValidAlert').addClass('d-none');
                    const successMsg = (res && res.message) ? res.message : 'Vote submitted successfully!';
                    $('#voteSubmittedMsg').text(successMsg);
                    $('#voteSubmittedAlert').removeClass('d-none').hide().fadeIn(200);
                    $verifyBtn.html('<i class="bi bi-check-circle-fill me-1"></i> Vote Submitted Successfully!');

                    setTimeout(function () {
                        const otpModalEl = document.getElementById('otpModal');
                        const otpModalInstance = bootstrap.Modal.getInstance(otpModalEl);
                        if (otpModalInstance) {
                            otpModalInstance.hide();
                        }
                        $('#voteForm')[0].reset();
                        $('#otpForm')[0].reset();
                    }, 1600);
                }, 800);
            },
            error: function (xhr) {
                $verifyBtn.prop('disabled', false).html('<span>Verify OTP &amp; Cast Vote</span> <i class="bi bi-check-lg ms-1"></i>');
                $('.otp-digit-box').addClass('is-invalid');
                const errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Invalid OTP code. Please try again.';
                $('#otpInvalidMsg').text(errMsg);
                $('#otpInvalidAlert').removeClass('d-none').hide().fadeIn(200);
                setTimeout(function () {
                    $('.otp-digit-box[data-index="1"]').focus().select();
                }, 300);
            }
        });
    });

    // Resend OTP Button Click
    $('#btnResendOtp').on('click', function () {
        if (!pendingVoteData.mobile || !pendingVoteData.pujaId) return;

        const $resendBtn = $(this);
        $resendBtn.prop('disabled', true);
        $('#otpInvalidAlert, #otpValidAlert, #voteSubmittedAlert').addClass('d-none');

        $.ajax({
            url: (window.apiBaseUrl || '/api/v1') + '/otp/send',
            type: 'POST',
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            data: JSON.stringify({
                phone: pendingVoteData.mobile,
                purpose: 'voting',
                event_id: pendingVoteData.eventId ? parseInt(pendingVoteData.eventId) : null,
                participant_id: parseInt(pendingVoteData.pujaId),
                award_id: pendingVoteData.awardId ? parseInt(pendingVoteData.awardId) : null
            }),
            success: function (res) {
                $('.otp-digit-box').val('').removeClass('is-invalid');
                $('#otpResendMsg').text((res && res.message) ? res.message : 'A new verification code has been dispatched to your mobile number!');
                $('#otpResendAlert').removeClass('d-none').hide().fadeIn(200).delay(3500).fadeOut(300);
                $('.otp-digit-box[data-index="1"]').focus();
                setTimeout(() => $resendBtn.prop('disabled', false), 5000);
            },
            error: function (xhr) {
                $resendBtn.prop('disabled', false);
                const errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to resend OTP at this time.';
                $('#otpInvalidMsg').text(errMsg);
                $('#otpInvalidAlert').removeClass('d-none').hide().fadeIn(200);
            }
        });
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
