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
    // 2. VOTING MODAL PRE-SELECTION & FORM SUBMISSION
    // ==========================================================================
    $('.vote-trigger-btn').on('click', function () {
        var pandalName = $(this).attr('data-pandal');
        $('#pandalSelect').val(pandalName);
        var voteModal = new bootstrap.Modal(document.getElementById('voteModal'));
        voteModal.show();
    });

    $('#voteForm').on('submit', function (e) {
        e.preventDefault();
        var submitBtn = $(this).find('button[type="submit"]');

        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Recording...');

        setTimeout(function () {
            var currentTotal = parseInt($('#liveTotalCounter').text().replace(/,/g, '')) || 0;
            $('#liveTotalCounter').text((currentTotal + 1).toLocaleString());

            $('#voteAlert').removeClass('d-none').hide().fadeIn(300);
            submitBtn.prop('disabled', false).html('<span>Vote Submitted!</span> <i class="bi bi-check2"></i>');

            setTimeout(function () {
                var modalEl = document.getElementById('voteModal');
                var modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) {
                    modalInstance.hide();
                }
                $('#voteForm')[0].reset();
                $('#voteAlert').addClass('d-none');
                submitBtn.html('<span>Submit My Vote</span> <i class="bi bi-arrow-right"></i>');
            }, 1500);

        }, 800);
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
    let currentReelIndex = 0;
    let shortsModalInstance = null;
    let isLikedState = {};

    function getShortsModal() {
        if (!shortsModalInstance) {
            const modalEl = document.getElementById('shortsVideoModal');
            if (modalEl) {
                shortsModalInstance = new bootstrap.Modal(modalEl);
            }
        }
        return shortsModalInstance;
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

        // Set Video Sources
        if (videoElem) {
            videoElem.pause();
            if (splash) videoElem.poster = splash;
            videoElem.src = videoUrl;
            videoElem.currentTime = 0;
            videoElem.muted = false; // default to sound on

            // Update Audio Button Display
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

    // Tap Video to Toggle Play/Pause
    $('#shortsVideoElement, #shortsPlayerContainer').on('click', function (e) {
        // Prevent click if clicking direct controls/buttons
        if ($(e.target).closest('.shorts-actions, .shorts-header, .shorts-footer, .shorts-nav-btn').length > 0) {
            return;
        }

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
        if (videoElem && videoElem.duration) {
            const rect = this.getBoundingClientRect();
            const clickPos = (e.clientX - rect.left) / rect.width;
            videoElem.currentTime = clickPos * videoElem.duration;
        }
    });

    // Mute / Unmute Sound Button
    $('#shortsAudioBtn').on('click', function (e) {
        e.stopPropagation();
        if (videoElem) {
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
        });
    }

});