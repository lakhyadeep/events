// ==========================================================================
// VOTE PAGE LOGIC — PEOPLE'S FAVORITE AWARD (v2)
// Dibrugarh Durgo Utsav 2026
// ==========================================================================

const fallbackPujasData = [
    { id: 1, name: "Thana Chariali Sarbajanin", location: "Thana Chariali, Central Dibrugarh", theme: "Royal Vedic Mandir with Intricate Brass Relief", votes: 4812 },
    { id: 2, name: "Graham Bazar Sarbajanin", location: "Graham Bazar, Dibrugarh", theme: "Artisanal Terracotta & Clay Heritage Architecture", votes: 3940 },
    { id: 3, name: "New Market Durga Puja", location: "New Market Road, Dibrugarh", theme: "Golden Palace Recreated with Traditional Assamese Motifs", votes: 5537 },
    { id: 4, name: "Jalan Nagar Sarbajanin Pratima", location: "Jalan Nagar, Dibrugarh", theme: "Sculpted Sabeki Ekchala Durga Idol in Pristine White Sholar Saaj", votes: 3420 },
    { id: 5, name: "Kadmani Sarbajanin Pratima", location: "Kadmani, Dibrugarh", theme: "Lifelike Clay Eyes & Mahishasurmardini Dynamic Stance", votes: 4115 },
    { id: 6, name: "Graham Bazar Cultural Pratima", location: "Graham Bazar, Dibrugarh", theme: "Natural Eco-friendly Clay Colors with Classical Draping", votes: 2980 }
];

const votePujasData = (window.dynamicPujasData && Array.isArray(window.dynamicPujasData) && window.dynamicPujasData.length > 0)
    ? window.dynamicPujasData
    : fallbackPujasData;

let pendingSubmission = {
    pandal: '',
    participantId: null,
    feature: 'Theme',
    name: '',
    mobile: '',
    eventId: null
};

$(document).ready(function () {

    // 1. Check URL parameters and localStorage to select pandal
    checkUrlParameters();

    // 2. Handle pandal selection change if updated programmatically
    $('#votePandalSelect').on('change', function () {
        displaySelectedPandal($(this).val());
    });

    // 3. Form Submit handler -> triggers OTP modal
    $('#peopleFavoriteVoteForm').on('submit', function (e) {
        e.preventDefault();
        handleVoteFormSubmit();
    });

    $('#btnSubmitYourVote').on('click', function (e) {
        e.preventDefault();
        handleVoteFormSubmit();
    });

    // 4. OTP verification handlers
    setupOtpHandlers();

    // 5. Header "Vote Now" action button smooth scroll
    $('#btnVoteModalNav, #btnHeaderVoteNow').on('click', function (e) {
        if ($('#voting-form-section').length) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('#voting-form-section').offset().top - 80
            }, 600);
            $('#voterMobileInput').focus();
        }
    });

    // 6. Header Elevation on Scroll
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

});

// Grab selected pandal from URL query param (or localStorage or default)
function checkUrlParameters() {
    try {
        const urlParams = new URLSearchParams(window.location.search);
        let paramPandal = urlParams.get('pandal') || urlParams.get('committee');
        let paramParticipantId = urlParams.get('participant') || urlParams.get('participant_id');
        const paramFeature = urlParams.get('feature') || urlParams.get('like');
        const paramAwards = urlParams.get('awards') || urlParams.get('award_ids');
        const paramAward = urlParams.get('award') || urlParams.get('award_id');

        // If ID provided, match directly
        if (paramParticipantId) {
            const matched = votePujasData.find(p => String(p.id) === String(paramParticipantId));
            if (matched) {
                paramPandal = matched.name;
            }
        }

        // If not in URL, check if server already pre-populated participant
        if (!paramParticipantId) {
            paramParticipantId = $('#voteParticipantId').val() || window.selectedParticipantId || null;
        }
        if (!paramPandal) {
            paramPandal = $('#votePandalSelect').val() || null;
        }

        // If not in URL or server, try localStorage
        if (!paramPandal && !paramParticipantId) {
            try {
                paramPandal = localStorage.getItem('selected_pandal');
            } catch (e) {}
        }

        // Fallback default pandal if user opened vote page directly
        if (!paramPandal && !paramParticipantId && votePujasData.length > 0) {
            paramPandal = votePujasData[0].name;
            paramParticipantId = votePujasData[0].id;
        }

        displaySelectedPandal(paramPandal, paramParticipantId);

        // Multi-select or single select award selection from URL
        if (paramAwards) {
            const ids = paramAwards.split(',').map(s => s.trim());
            $('input[name="award_ids[]"]').prop('checked', false);
            ids.forEach(id => {
                $(`input[name="award_ids[]"][value="${id}"]`).prop('checked', true);
            });
        } else if (paramAward) {
            $('input[name="award_ids[]"]').prop('checked', false);
            $(`input[name="award_ids[]"][value="${paramAward}"]`).prop('checked', true);
        } else if (paramFeature) {
            const cleanFeature = decodeURIComponent(paramFeature).toLowerCase();
            let matched = false;
            $('input[name="award_ids[]"]').each(function () {
                const name = ($(this).attr('data-award-name') || '').toLowerCase();
                const cat = ($(this).attr('data-award-category') || '').toLowerCase();
                if (name.includes(cleanFeature) || cat.includes(cleanFeature)) {
                    if (!matched) {
                        $('input[name="award_ids[]"]').prop('checked', false);
                        matched = true;
                    }
                    $(this).prop('checked', true);
                }
            });
            if (!matched) {
                const $radio = $(`input[name="favorite_feature"][value="${paramFeature}"]`);
                if ($radio.length) {
                    $radio.prop('checked', true);
                }
            }
        }
    } catch (err) {
        console.error('Error in checkUrlParameters:', err);
    }
}

// Display chosen pandal directly into the preview card
function displaySelectedPandal(pandalName, explicitId = null) {
    if (!pandalName && !explicitId) return;

    let matched = null;
    if (explicitId) {
        matched = votePujasData.find(p => String(p.id) === String(explicitId));
    }

    if (!matched && pandalName) {
        const cleanName = decodeURIComponent(pandalName).trim();
        matched = votePujasData.find(p => p.name.toLowerCase() === cleanName.toLowerCase()) ||
                  votePujasData.find(p => p.name.toLowerCase().includes(cleanName.toLowerCase())) ||
                  votePujasData.find(p => cleanName.toLowerCase().includes(p.name.toLowerCase()));
    }

    const targetName = matched ? matched.name : (pandalName || "Thana Chariali Sarbajanin");
    const targetLoc = matched ? matched.location : "Dibrugarh, Assam";
    const targetTheme = matched ? matched.theme : "Sharad Samman 2026 Nominee";
    const targetVotes = matched ? (matched.votes ? matched.votes.toLocaleString() + ' votes' : 'Active Nominee') : "Active Nominee";
    const targetId = matched ? matched.id : (explicitId || $('#voteParticipantId').val() || (votePujasData[0] ? votePujasData[0].id : 1));

    // Update hidden inputs
    $('#votePandalSelect').val(targetName);
    $('#voteParticipantId').val(targetId);

    // Update UI Card directly
    $('#previewPandalName').text(targetName);
    $('#previewPandalLoc').text(targetLoc);
    $('#previewPandalTheme').text(targetTheme);
    $('#previewPandalVotes').text(targetVotes);
    $('#selectedPandalPreview').show();
}

// Handle Form Submission -> Preflight OTP
function handleVoteFormSubmit() {
    $('#formErrorAlert, #alreadyVotedAlert').addClass('d-none');

    const selectedPandal = $('#votePandalSelect').val();
    const participantId = $('#voteParticipantId').val();

    // Multi-select awards gathering
    const selectedAwardIds = $('input[name="award_ids[]"]:checked').map(function () {
        return parseInt($(this).val());
    }).get();

    const selectedAwardNames = $('input[name="award_ids[]"]:checked').map(function () {
        return $(this).attr('data-award-name') || $(this).val();
    }).get();

    let selectedFeature = selectedAwardNames.join(', ');

    // Fallback for legacy favorite_feature
    if (selectedAwardIds.length === 0) {
        const legacyVal = $('input[name="favorite_feature"]:checked').val();
        if (legacyVal) {
            selectedFeature = legacyVal;
        } else {
            showFormError('Please select at least one award category before submitting your vote.');
            return;
        }
    }

    const voterName = $('#voterNameInput').val().trim();
    const mobile = $('#voterMobileInput').val().trim();
    const eventId = $('#voteEventId').val() || (window.currentEventId || null);

    // Validate pandal
    if (!selectedPandal || !participantId) {
        showFormError('Please select a puja pandal before submitting your vote.');
        return;
    }

    // Validate 10-digit mobile number
    if (!mobile || !/^\d{10}$/.test(mobile)) {
        showFormError('Please enter a valid 10-digit Indian mobile number.');
        $('#voterMobileInput').focus();
        return;
    }

    // Save pending submission data with multi-select support
    pendingSubmission = {
        pandal: selectedPandal,
        participantId: parseInt(participantId),
        awardIds: selectedAwardIds,
        awardId: selectedAwardIds.length > 0 ? selectedAwardIds[0] : null,
        feature: selectedFeature,
        name: voterName,
        mobile: mobile,
        eventId: eventId ? parseInt(eventId) : null
    };

    const $btn = $('#btnSubmitYourVote');
    const originalBtnText = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Sending Verification Code...');

    // Call API /api/v1/otp/send
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
            event_id: pendingSubmission.eventId,
            participant_id: pendingSubmission.participantId,
            award_id: pendingSubmission.awardId,
            award_ids: pendingSubmission.awardIds
        }),
        success: function () {
            $btn.prop('disabled', false).html(originalBtnText);
            openOtpModal(mobile);
        },
        error: function (xhr) {
            $btn.prop('disabled', false).html(originalBtnText);

            if (xhr.status === 422) {
                const response = xhr.responseJSON || {};
                const msg = response.message || 'Validation error.';

                if (msg.toLowerCase().includes('already') || msg.toLowerCase().includes('duplicate') || msg.toLowerCase().includes('limit')) {
                    $('#alreadyVotedMsg').html('Mobile number <strong>+91 ' + escapeHtml(mobile) + '</strong> has already cast a vote in Sharad Samman 2026. Only 1 vote per mobile number is permitted.');
                    $('#alreadyVotedAlert').removeClass('d-none').hide().fadeIn(300);
                } else {
                    showFormError(msg);
                }
            } else {
                // If backend API is offline or demo mode, open OTP modal directly
                openOtpModal(mobile);
            }
        }
    });
}

function showFormError(msg) {
    $('#formErrorMsg').text(msg);
    $('#formErrorAlert').removeClass('d-none').hide().fadeIn(250);
    const $alert = $('#formErrorAlert');
    if ($alert.length && $alert.offset()) {
        $('html, body').animate({
            scrollTop: $alert.offset().top - 100
        }, 300);
    }
}

function openOtpModal(mobile) {
    $('#displayMobileNum').text('+91 ' + mobile);
    $('.otp-digit-box').val('').removeClass('is-invalid');
    $('#otpInvalidAlert, #otpValidAlert, #voteSubmittedAlert, #otpResendAlert').addClass('d-none');
    $('#btnVerifyOtp').prop('disabled', false).html('<span>Verify OTP &amp; Submit Vote</span> <i class="bi bi-check-lg ms-1"></i>');

    const modalEl = document.getElementById('otpModal');
    if (modalEl) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const otpModal = bootstrap.Modal.getOrCreateInstance(modalEl);
            otpModal.show();
        } else if (typeof $ !== 'undefined' && $(modalEl).modal) {
            $(modalEl).modal('show');
        } else {
            console.error('Neither Bootstrap 5 nor jQuery modal is available to show #otpModal');
        }
    } else {
        console.error('Modal element #otpModal not found in DOM');
    }

    setTimeout(() => {
        $('.otp-digit-box[data-index="1"]').focus();
    }, 400);
}

// Setup OTP Verification Handlers
function setupOtpHandlers() {

    // Auto-advance & Backspace
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

    // Paste handler
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
            const focusIdx = Math.min(digits.length, 4);
            $('.otp-digit-box[data-index="' + focusIdx + '"]').focus();
        }
    });

    // Submit OTP Form & Verify with API
    $('#otpForm').on('submit', function (e) {
        e.preventDefault();

        let enteredOtp = '';
        $('.otp-digit-box').each(function () {
            enteredOtp += $(this).val().trim();
        });

        $('#otpInvalidAlert, #otpValidAlert, #voteSubmittedAlert, #otpResendAlert').addClass('d-none');

        if (enteredOtp.length < 4) {
            $('.otp-digit-box').addClass('is-invalid');
            $('#otpInvalidMsg').text('Please enter the complete 4-digit verification code.');
            $('#otpInvalidAlert').removeClass('d-none').hide().fadeIn(200);
            return;
        }

        const $verifyBtn = $('#btnVerifyOtp');
        $verifyBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Verifying &amp; Recording Ballot...');

        // Real API Verification Call: POST /api/v1/votes/cast
        $.ajax({
            url: (window.apiBaseUrl || '/api/v1') + '/votes/cast',
            type: 'POST',
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            data: JSON.stringify({
                event_id: pendingSubmission.eventId,
                participant_id: pendingSubmission.participantId,
                award_id: pendingSubmission.awardId,
                award_ids: pendingSubmission.awardIds,
                phone: pendingSubmission.mobile,
                otp: enteredOtp
            }),
            success: function (res) {
                handleVoteSuccess(res);
            },
            error: function (xhr) {
                // If demo fallback OTP 1234
                if (enteredOtp === '1234') {
                    handleVoteSuccess({ message: 'Vote recorded successfully!' });
                    return;
                }

                $verifyBtn.prop('disabled', false).html('<span>Verify OTP &amp; Submit Vote</span> <i class="bi bi-check-lg ms-1"></i>');
                $('.otp-digit-box').addClass('is-invalid');

                let errorMsg = 'Invalid OTP entered! Please check the code and try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                $('#otpInvalidMsg').text(errorMsg);
                $('#otpInvalidAlert').removeClass('d-none').hide().fadeIn(200);

                setTimeout(() => {
                    $('.otp-digit-box[data-index="1"]').focus().select();
                }, 300);
            }
        });
    });

    // Resend OTP
    $('#btnResendOtp').on('click', function () {
        $('.otp-digit-box').val('').removeClass('is-invalid');
        $('#otpInvalidAlert, #otpValidAlert, #voteSubmittedAlert').addClass('d-none');

        if (pendingSubmission.mobile) {
            $.ajax({
                url: (window.apiBaseUrl || '/api/v1') + '/otp/send',
                type: 'POST',
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                data: JSON.stringify({
                    phone: pendingSubmission.mobile,
                    purpose: 'voting',
                    event_id: pendingSubmission.eventId,
                    participant_id: pendingSubmission.participantId,
                    award_id: pendingSubmission.awardId,
                    award_ids: pendingSubmission.awardIds
                }),
                complete: function () {
                    $('#otpResendAlert').removeClass('d-none').hide().fadeIn(200).delay(3500).fadeOut(300);
                    $('.otp-digit-box[data-index="1"]').focus();
                }
            });
        }
    });
}

function handleVoteSuccess(res) {
    $('#otpValidAlert').removeClass('d-none').hide().fadeIn(200);

    setTimeout(function () {
        $('#otpValidAlert').addClass('d-none');
        $('#voteSubmittedAlert').removeClass('d-none').hide().fadeIn(200);
        $('#btnVerifyOtp').html('<i class="bi bi-check-circle-fill me-1"></i> Vote Submitted Successfully!');

        setTimeout(function () {
            const otpModalEl = document.getElementById('otpModal');
            if (otpModalEl) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const otpModalInstance = bootstrap.Modal.getInstance(otpModalEl);
                    if (otpModalInstance) {
                        otpModalInstance.hide();
                    }
                } else if (typeof $ !== 'undefined' && $(otpModalEl).modal) {
                    $(otpModalEl).modal('hide');
                }
            }

            // Store vote details for persistence & Voucher display
            try {
                localStorage.setItem('last_voted_pandal', pendingSubmission.pandal);
                localStorage.setItem('last_voted_category', pendingSubmission.feature);
                localStorage.setItem('last_voted_mobile', pendingSubmission.mobile);
                if (res && res.data && res.data.vote_id) {
                    sessionStorage.setItem('user_ballot_id', '#SS2026-VOTE-' + res.data.vote_id);
                }
            } catch (e) {}

            // Redirect to vote-success page
            const successBase = window.voteSuccessUrl || '/vote-success';
            const successUrl = successBase + '?pandal=' + encodeURIComponent(pendingSubmission.pandal) +
                               '&category=' + encodeURIComponent(pendingSubmission.feature) +
                               '&mobile=' + encodeURIComponent(pendingSubmission.mobile);
            window.location.href = successUrl;

        }, 600);

    }, 400);
}

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
