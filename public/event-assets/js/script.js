$(document).ready(function () {

    // ==========================================================================
    // 1. NAVIGATION ACTIVE STATE & SMOOTH COLLAPSE
    // ==========================================================================
    $('.custom-nav .nav-link').on('click', function () {
        $('.custom-nav .nav-link').removeClass('active');
        $(this).addClass('active');

        if ($('.navbar-collapse').hasClass('show')) {
            $('.navbar-toggler').trigger('click');
        }
    });

    // ==========================================================================
    // 2. VOTING MODAL PRE-SELECTION, ELIGIBILITY CHECK & OTP VERIFICATION
    // ==========================================================================
    var pendingVoteData = { mobile: '', participantId: '', awardId: '', eventId: '' };

    $(document).on('click', '.vote-trigger-btn', function (e) {
        e.preventDefault();
        var pandalName = $(this).attr('data-pandal') || '';
        var participantId = $(this).attr('data-participant-id') || $(this).attr('data-puja-id') || '';
        var participantSlug = $(this).attr('data-slug') || '';

        try {
            localStorage.setItem('selected_pandal', pandalName);
        } catch (err) {}

        var baseUrl = (window.voteBaseUrl || '/vote').replace(/\/+$/, '');
        var voteUrl = baseUrl;
        if (participantSlug) {
            voteUrl += '/' + encodeURIComponent(participantSlug);
        } else if (pandalName) {
            var slugFallback = pandalName.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
            voteUrl += slugFallback ? '/' + encodeURIComponent(slugFallback) : '';
        } else if (participantId) {
            voteUrl += '/' + encodeURIComponent(participantId);
        }
        window.location.href = voteUrl;
    });

    // Step 1: Submit Mobile Number to trigger real OTP or show Already Voted alert
    $('#voteForm').on('submit', function (e) {
        e.preventDefault();
        $('#alreadyVotedAlert').addClass('d-none');

        var rawMobile = $('#voterMobile').val().trim();
        var mobile = rawMobile.replace(/[^0-9]/g, '');
        if (mobile.length === 12 && mobile.startsWith('91')) {
            mobile = mobile.substring(2);
        } else if (mobile.length === 11 && mobile.startsWith('0')) {
            mobile = mobile.substring(1);
        }

        var participantId = $('#pandalSelect').val();
        var awardId = $('#awardSelect').val() || null;
        var eventId = $('#voteEventId').val() || null;

        if (mobile.length !== 10 || !/^[6-9]\d{9}$/.test(mobile)) {
            alert('Please enter a valid 10-digit Indian mobile number (starting with 6, 7, 8, or 9).');
            return;
        }

        if (!participantId) {
            alert('Please select a committee to vote for.');
            return;
        }

        pendingVoteData.mobile = mobile;
        pendingVoteData.participantId = participantId;
        pendingVoteData.awardId = awardId;
        pendingVoteData.eventId = eventId;

        var $btn = $('#btnSendOtp');
        var originalBtnHtml = $btn.html();
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

                var voteModalEl = document.getElementById('voteModal');
                var voteModalInstance = bootstrap.Modal.getInstance(voteModalEl);
                if (voteModalInstance) {
                    voteModalInstance.hide();
                }

                $('#displayMobileNum').text('+91 ' + mobile);
                $('.otp-digit-box').val('').removeClass('is-invalid');
                $('#otpInvalidAlert, #otpValidAlert, #voteSubmittedAlert, #otpResendAlert').addClass('d-none');
                $('#btnVerifyOtp').prop('disabled', false).html('<span>Verify OTP &amp; Cast Vote</span> <i class="bi bi-check-lg ms-1"></i>');

                setTimeout(function () {
                    var otpModal = new bootstrap.Modal(document.getElementById('otpModal'));
                    otpModal.show();
                    setTimeout(function () {
                        $('.otp-digit-box[data-index="1"]').focus();
                    }, 300);
                }, 300);
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html(originalBtnHtml);
                var errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to proceed with vote. Please try again.';
                $('#alreadyVotedMsg').html(errMsg);
                $('#alreadyVotedAlert').removeClass('d-none').hide().fadeIn(300);
            }
        });
    });

    // Dynamic OTP Input Boxes Auto-Tab & Backspace Logic
    var totalOtpDigits = $('.otp-digit-box').length || 6;
    $('.otp-digit-box').on('input keyup', function (e) {
        var $this = $(this);
        var val = $this.val();
        var index = parseInt($this.attr('data-index'));

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
        var pasteData = (e.originalEvent.clipboardData || window.clipboardData).getData('text').trim();
        var digits = pasteData.replace(/\D/g, '').split('');
        if (digits.length > 0) {
            $('.otp-digit-box').each(function (idx) {
                if (digits[idx]) {
                    $(this).val(digits[idx]).removeClass('is-invalid');
                }
            });
            var focusIdx = Math.min(digits.length, totalOtpDigits);
            $('.otp-digit-box[data-index="' + focusIdx + '"]').focus();
        }
    });

    // Step 2: Submit OTP Form & Real Verification Handler
    $('#otpForm').on('submit', function (e) {
        e.preventDefault();

        var enteredOtp = '';
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

        var $verifyBtn = $('#btnVerifyOtp');
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
                participant_id: parseInt(pendingVoteData.participantId),
                award_id: pendingVoteData.awardId ? parseInt(pendingVoteData.awardId) : null,
                phone: pendingVoteData.mobile,
                otp: enteredOtp
            }),
            success: function (res) {
                $('#otpValidAlert').removeClass('d-none').hide().fadeIn(200);

                var participantId = pendingVoteData.participantId;
                $('.live-vote-count-' + participantId).each(function () {
                    var current = parseInt($(this).text().replace(/[^0-9]/g, '')) || 0;
                    $(this).html('<i class="bi bi-hand-thumbs-up-fill me-1"></i>' + (current + 1).toLocaleString());
                });

                var $totalCounter = $('#liveTotalCounter');
                if ($totalCounter.length) {
                    var currentTotal = parseInt($totalCounter.text().replace(/,/g, '')) || 0;
                    $totalCounter.text((currentTotal + 1).toLocaleString());
                }

                setTimeout(function () {
                    $('#otpValidAlert').addClass('d-none');
                    var successMsg = (res && res.message) ? res.message : 'Vote submitted successfully!';
                    $('#voteSubmittedMsg').text(successMsg);
                    $('#voteSubmittedAlert').removeClass('d-none').hide().fadeIn(200);
                    $verifyBtn.html('<i class="bi bi-check-circle-fill me-1"></i> Vote Submitted Successfully!');

                    setTimeout(function () {
                        var otpModalEl = document.getElementById('otpModal');
                        var otpModalInstance = bootstrap.Modal.getInstance(otpModalEl);
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
                var errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Invalid OTP code. Please try again.';
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
        if (!pendingVoteData.mobile || !pendingVoteData.participantId) return;

        var $resendBtn = $(this);
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
                participant_id: parseInt(pendingVoteData.participantId),
                award_id: pendingVoteData.awardId ? parseInt(pendingVoteData.awardId) : null
            }),
            success: function (res) {
                $('.otp-digit-box').val('').removeClass('is-invalid');
                $('#otpResendMsg').text((res && res.message) ? res.message : 'A new verification code has been dispatched to your mobile number!');
                $('#otpResendAlert').removeClass('d-none').hide().fadeIn(200).delay(3500).fadeOut(300);
                $('.otp-digit-box[data-index="1"]').focus();
                setTimeout(function () { $resendBtn.prop('disabled', false); }, 5000);
            },
            error: function (xhr) {
                $resendBtn.prop('disabled', false);
                var errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to resend OTP at this time.';
                $('#otpInvalidMsg').text(errMsg);
                $('#otpInvalidAlert').removeClass('d-none').hide().fadeIn(200);
            }
        });
    });

    // ==========================================================================
    // 3. NAVBAR ELEVATION ON SCROLL
    // ==========================================================================
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

    // ==========================================================================
    // 4. GLIMPSES CAROUSEL HORIZONTAL SCROLL & DRAG
    // ==========================================================================
    const reelsViewportEl = document.getElementById('reelsViewport');
    const $prevReelBtn = $('#prevReelBtn');
    const $nextReelBtn = $('#nextReelBtn');

    function updateReelNavState() {
        if (!reelsViewportEl) return;
        const scrollLeft = reelsViewportEl.scrollLeft;
        const maxScroll = reelsViewportEl.scrollWidth - reelsViewportEl.clientWidth;

        if (scrollLeft <= 8) {
            $prevReelBtn.addClass('disabled');
        } else {
            $prevReelBtn.removeClass('disabled');
        }

        if (scrollLeft >= maxScroll - 8) {
            $nextReelBtn.addClass('disabled');
        } else {
            $nextReelBtn.removeClass('disabled');
        }
    }

    if (reelsViewportEl) {
        reelsViewportEl.addEventListener('scroll', updateReelNavState, { passive: true });
        updateReelNavState();
        $(window).on('resize', updateReelNavState);
    }

    $prevReelBtn.on('click', function (e) {
        e.preventDefault();
        if (reelsViewportEl) {
            const scrollDist = Math.max(260, reelsViewportEl.clientWidth * 0.65);
            reelsViewportEl.scrollBy({ left: -scrollDist, behavior: 'smooth' });
        }
    });

    $nextReelBtn.on('click', function (e) {
        e.preventDefault();
        if (reelsViewportEl) {
            const scrollDist = Math.max(260, reelsViewportEl.clientWidth * 0.65);
            reelsViewportEl.scrollBy({ left: scrollDist, behavior: 'smooth' });
        }
    });

    // Mouse Drag-to-Scroll functionality for Desktop
    let isCarouselDragging = false;
    let dragStartX = 0;
    let dragScrollLeft = 0;
    let hasDraggedDistance = false;

    $('#reelsViewport').on('mousedown', function (e) {
        if (e.button !== 0) return;
        isCarouselDragging = true;
        hasDraggedDistance = false;
        $(this).addClass('active-drag');
        dragStartX = e.pageX - this.offsetLeft;
        dragScrollLeft = this.scrollLeft;
    }).on('mouseleave mouseup', function () {
        isCarouselDragging = false;
        $(this).removeClass('active-drag');
    }).on('mousemove', function (e) {
        if (!isCarouselDragging) return;
        const x = e.pageX - this.offsetLeft;
        const walk = (x - dragStartX) * 1.4;
        if (Math.abs(x - dragStartX) > 6) {
            hasDraggedDistance = true;
        }
        this.scrollLeft = dragScrollLeft - walk;
    });

    // Keyboard navigation when focusing reels section
    $(document).on('keydown', function (e) {
        if ($('#shortsVideoModal').hasClass('show')) return;
        const isReelsInView = reelsViewportEl && (
            reelsViewportEl.getBoundingClientRect().top < window.innerHeight &&
            reelsViewportEl.getBoundingClientRect().bottom > 0
        );
        if (!isReelsInView) return;

        if (e.key === 'ArrowLeft') {
            reelsViewportEl.scrollBy({ left: -260, behavior: 'smooth' });
        } else if (e.key === 'ArrowRight') {
            reelsViewportEl.scrollBy({ left: 260, behavior: 'smooth' });
        }
    });

    // ==========================================================================
    // 5. REELS / SHORTS VIDEO MODAL FUNCTIONALITY
    // ==========================================================================
    const $cards = $('.reel-card');
    const videoElem = document.getElementById('shortsVideoElement');
    const iframeElem = document.getElementById('shortsIframeElement');
    let currentReelIndex = 0;
    let shortsModalInstance = null;
    let isLikedState = {};
    let isYtMuted = false;
    let currentMode = 'youtube'; // 'youtube' or 'video'

    function getShortsModal() {
        if (!shortsModalInstance) {
            const modalEl = document.getElementById('shortsVideoModal');
            if (modalEl) {
                shortsModalInstance = new bootstrap.Modal(modalEl);
            }
        }
        return shortsModalInstance;
    }

    function sendYouTubeCommand(func, args) {
        if (iframeElem && iframeElem.contentWindow) {
            iframeElem.contentWindow.postMessage(JSON.stringify({
                event: 'command',
                func: func,
                args: args || []
            }), '*');
        }
    }

    function loadReel(index) {
        if (index < 0) index = $cards.length - 1;
        if (index >= $cards.length) index = 0;

        currentReelIndex = index;
        const $card = $cards.eq(index);

        const pandal = $card.attr('data-pandal') || 'Dibrugarh Pandal';
        const likes = $card.attr('data-likes') || '10.5K';
        const theme = $card.attr('data-theme') || 'Sharad Utsav Celebration';
        const splash = $card.attr('data-splash') || '';
        const videoUrl = $card.attr('data-video') || '';
        const embedUrl = $card.attr('data-embed') || '';
        const platform = $card.attr('data-platform') || 'youtube_shorts';

        // Update Text Info
        $('#shortsModalPandalName').text(pandal);
        $('#shortsModalTheme').text('Theme: ' + theme);
        $('#shortsLikeCount').text(likes);

        // Update Like Button State
        if (isLikedState[index]) {
            $('#shortsLikeBtn .action-circle-icon').addClass('text-danger').removeClass('text-white');
            $('#shortsLikeBtn i').attr('class', 'bi bi-heart-fill');
        } else {
            $('#shortsLikeBtn .action-circle-icon').removeClass('text-danger').addClass('text-white');
            $('#shortsLikeBtn i').attr('class', 'bi bi-heart');
        }

        const isDirect = (platform === 'direct' || videoUrl.toLowerCase().endsWith('.mp4')) && !embedUrl.includes('youtube');

        if (isDirect) {
            currentMode = 'video';
            if (iframeElem) {
                $(iframeElem).addClass('d-none').attr('src', '');
            }
            if (videoElem) {
                $(videoElem).removeClass('d-none');
                videoElem.pause();
                if (splash) videoElem.poster = splash;
                videoElem.src = videoUrl;
                videoElem.currentTime = 0;
                videoElem.muted = false;

                $('#audioIcon').attr('class', 'bi bi-volume-up-fill');
                $('#audioLabel').text('Sound On');

                videoElem.play().then(() => {
                    showCenterIndicator('bi-play-fill');
                }).catch(err => {
                    console.log('Autoplay muted fallback:', err);
                    videoElem.muted = true;
                    $('#audioIcon').attr('class', 'bi bi-volume-mute-fill');
                    $('#audioLabel').text('Muted');
                    videoElem.play().catch(() => {});
                });
            }
            $('#shortsProgressBar').show();
        } else {
            currentMode = 'youtube';
            if (videoElem) {
                videoElem.pause();
                $(videoElem).addClass('d-none');
            }
            if (iframeElem) {
                $(iframeElem).removeClass('d-none');

                let targetSrc = embedUrl;
                if (!targetSrc && videoUrl) {
                    const match = videoUrl.match(/(?:shorts\/|youtu\.be\/|v=)([\w\-]+)/);
                    if (match) {
                        targetSrc = 'https://www.youtube-nocookie.com/embed/' + match[1] + '?autoplay=1&enablejsapi=1&rel=0&playsinline=1&modestbranding=1&loop=1&playlist=' + match[1];
                    } else {
                        targetSrc = videoUrl;
                    }
                }
                iframeElem.src = targetSrc;
                isYtMuted = false;
                $('#audioIcon').attr('class', 'bi bi-volume-up-fill');
                $('#audioLabel').text('Sound On');
            }
            $('#shortsProgressFill').css('width', '100%');
        }
    }

    // Helper for Tap Indicator Animation
    function showCenterIndicator(iconClass) {
        const $indicator = $('#centerPlayIndicator');
        $('#indicatorIcon').attr('class', 'bi ' + iconClass);
        $indicator.addClass('show-indicator');
        setTimeout(function () {
            $indicator.removeClass('show-indicator');
        }, 500);
    }

    // Hover to Preview Video on Reel Card Background
    $('.reel-card').on('mouseenter', function () {
        if (isCarouselDragging) return;
        const bgVideo = $(this).find('video.reel-bg')[0];
        if (bgVideo && bgVideo.paused) {
            bgVideo.play().catch(function () {});
        }
    }).on('mouseleave', function () {
        const bgVideo = $(this).find('video.reel-bg')[0];
        if (bgVideo && !bgVideo.paused) {
            bgVideo.pause();
        }
    });

    // Open Modal when clicking any Reel Card (unless dragging carousel)
    $('.reel-card').on('click', function () {
        if (hasDraggedDistance) {
            hasDraggedDistance = false;
            return;
        }
        const index = parseInt($(this).attr('data-index')) || 0;
        const modal = getShortsModal();
        if (modal) {
            loadReel(index);
            modal.show();
        }
    });

    // Tap Video to Toggle Play/Pause for direct video
    $('#shortsVideoElement').on('click', function (e) {
        if (videoElem) {
            if (videoElem.paused) {
                videoElem.play();
                showCenterIndicator('bi-play-fill');
            } else {
                videoElem.pause();
                showCenterIndicator('bi-pause-fill');
            }
        }
    });

    // Update Progress Bar as Video Plays
    if (videoElem) {
        videoElem.addEventListener('timeupdate', function () {
            if (videoElem.duration) {
                const pct = (videoElem.currentTime / videoElem.duration) * 100;
                $('#shortsProgressFill').css('width', pct + '%');
            }
        });

        videoElem.addEventListener('ended', function () {
            // Auto advance to next short when video ends
            loadReel(currentReelIndex + 1);
        });
    }

    // Scrub Progress Bar on Click
    $('#shortsProgressBar').on('click', function (e) {
        if (currentMode === 'video' && videoElem && videoElem.duration) {
            const rect = this.getBoundingClientRect();
            const clickPos = (e.clientX - rect.left) / rect.width;
            videoElem.currentTime = clickPos * videoElem.duration;
        }
    });

    // Mute / Unmute Sound Button
    $('#shortsAudioBtn').on('click', function (e) {
        e.stopPropagation();
        if (currentMode === 'youtube') {
            isYtMuted = !isYtMuted;
            if (isYtMuted) {
                sendYouTubeCommand('mute');
                $('#audioIcon').attr('class', 'bi bi-volume-mute-fill');
                $('#audioLabel').text('Muted');
            } else {
                sendYouTubeCommand('unMute');
                $('#audioIcon').attr('class', 'bi bi-volume-up-fill');
                $('#audioLabel').text('Sound On');
            }
        } else if (videoElem) {
            videoElem.muted = !videoElem.muted;
            if (videoElem.muted) {
                $('#audioIcon').attr('class', 'bi bi-volume-mute-fill');
                $('#audioLabel').text('Muted');
            } else {
                $('#audioIcon').attr('class', 'bi bi-volume-up-fill');
                $('#audioLabel').text('Sound On');
            }
        }
    });

    // Next Short Navigation
    $('#nextShortBtn').on('click', function (e) {
        e.stopPropagation();
        loadReel(currentReelIndex + 1);
    });

    // Previous Short Navigation
    $('#prevShortBtn').on('click', function (e) {
        e.stopPropagation();
        loadReel(currentReelIndex - 1);
    });

    // Like Button Toggle
    $('#shortsLikeBtn').on('click', function (e) {
        e.stopPropagation();
        isLikedState[currentReelIndex] = !isLikedState[currentReelIndex];
        if (isLikedState[currentReelIndex]) {
            $(this).find('.action-circle-icon').addClass('text-danger').removeClass('text-white');
            $(this).find('i').attr('class', 'bi bi-heart-fill');
            showCenterIndicator('bi-heart-fill text-danger');
        } else {
            $(this).find('.action-circle-icon').removeClass('text-danger').addClass('text-white');
            $(this).find('i').attr('class', 'bi bi-heart');
        }
    });

    // Share Button Action
    $('#shortsShareBtn').on('click', function (e) {
        e.stopPropagation();
        if (navigator.clipboard) {
            navigator.clipboard.writeText(window.location.href);
        }
        $('#shareToast').fadeIn(200).delay(1800).fadeOut(300);
    });

    // Modal Cleanup on Close
    const modalEl = document.getElementById('shortsVideoModal');
    if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', function () {
            if (videoElem) {
                videoElem.pause();
                videoElem.currentTime = 0;
            }
            if (iframeElem) {
                iframeElem.src = '';
            }
        });
    }

});