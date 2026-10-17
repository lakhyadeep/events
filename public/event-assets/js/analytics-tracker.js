/**
 * Dibrugarh Durgo Utsav - High-Performance Client Analytics Tracker
 * Captures user engagement, searches, map clicks, and conversions asynchronously.
 */
(function () {
    'use strict';

    function getCookie(name) {
        const value = '; ' + document.cookie;
        const parts = value.split('; ' + name + '=');
        if (parts.length === 2) {
            return parts.pop().split(';').shift();
        }
        return null;
    }

    let visitorId = getCookie('analytics_vid');
    if (!visitorId) {
        try {
            visitorId = localStorage.getItem('analytics_vid');
            if (!visitorId) {
                visitorId = 'v-' + Math.random().toString(36).substring(2, 15) + '-' + Date.now().toString(36);
                localStorage.setItem('analytics_vid', visitorId);
            }
        } catch (e) {
            visitorId = 'anon-' + Math.random().toString(36).substring(2, 10);
        }
    }

    window.trackAnalyticsEvent = function (actionType, options) {
        options = options || {};
        try {
            const payload = {
                action_type: actionType,
                event_slug: window.currentEventSlug || null,
                event_id: window.currentEventId || null,
                visitor_id: visitorId,
                target_type: options.targetType || null,
                target_id: options.targetId || null,
                metadata: options.metadata || {},
                url: window.location.href,
                device_type: window.innerWidth < 768 ? 'mobile' : (window.innerWidth < 1024 ? 'tablet' : 'desktop')
            };

            const endpoint = (window.apiBaseUrl || '/api/v1') + '/analytics/event';
            const jsonPayload = JSON.stringify(payload);

            if (navigator.sendBeacon) {
                const blob = new Blob([jsonPayload], { type: 'application/json' });
                navigator.sendBeacon(endpoint, blob);
            } else {
                fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: jsonPayload,
                    keepalive: true
                }).catch(function () {});
            }
        } catch (e) {
            // Silently swallow errors to guarantee unhindered UX
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        // 1. Debounced Search Tracking in Directory
        const searchInput = document.getElementById('pujaSearchInput');
        if (searchInput) {
            let searchTimer = null;
            let lastTrackedQuery = '';

            searchInput.addEventListener('input', function (e) {
                const query = (e.target.value || '').trim();
                clearTimeout(searchTimer);

                if (query.length >= 2 && query !== lastTrackedQuery) {
                    searchTimer = setTimeout(function () {
                        lastTrackedQuery = query;
                        window.trackAnalyticsEvent('search', {
                            metadata: { query: query }
                        });
                    }, 1200);
                }
            });
        }

        // 2. High-Performance Event Delegation
        document.body.addEventListener('click', function (e) {
            // Zone Filter Button Click
            const zoneBtn = e.target.closest('.filter-chip-btn');
            if (zoneBtn) {
                const zone = zoneBtn.getAttribute('data-zone');
                if (zone) {
                    window.trackAnalyticsEvent('zone_filter', {
                        target_type: 'zone',
                        metadata: { zone: zone }
                    });
                }
                return;
            }

            // Google Maps "Get Directions" click
            const mapLink = e.target.closest('a[href*="google.com/maps"], a[href*="maps.google.com"], .map-link, .btn-directions');
            if (mapLink) {
                const card = mapLink.closest('[data-id]') || mapLink.closest('.vote-card');
                const pujaId = card ? card.getAttribute('data-id') : null;
                const pujaName = card ? (card.getAttribute('data-pandal') || card.querySelector('h3, h4, h5')?.textContent?.trim()) : null;

                window.trackAnalyticsEvent('map_directions_click', {
                    target_type: 'participant',
                    target_id: pujaId ? parseInt(pujaId, 10) : null,
                    metadata: {
                        puja_name: pujaName,
                        map_url: mapLink.getAttribute('href')
                    }
                });
                return;
            }

            // Vote Button Click on a Puja Card
            const voteBtn = e.target.closest('a[href*="/vote/"], .btn-vote, button.vote-btn');
            if (voteBtn && !voteBtn.closest('form')) {
                const card = voteBtn.closest('[data-id]') || voteBtn.closest('.vote-card');
                const pujaId = card ? card.getAttribute('data-id') : null;
                const pujaName = card ? (card.getAttribute('data-pandal') || card.querySelector('h3, h4, h5')?.textContent?.trim()) : null;

                window.trackAnalyticsEvent('vote_initiated', {
                    target_type: 'participant',
                    target_id: pujaId ? parseInt(pujaId, 10) : null,
                    metadata: { puja_name: pujaName }
                });
                return;
            }

            // Sponsor / Banner Click
            const sponsorLink = e.target.closest('.sponsor-item a, .marketing-banner a, [data-track-banner]');
            if (sponsorLink) {
                const bannerId = sponsorLink.getAttribute('data-banner-id');
                const sponsorName = sponsorLink.getAttribute('data-name') || sponsorLink.getAttribute('title') || sponsorLink.textContent?.trim();

                window.trackAnalyticsEvent('banner_click', {
                    target_type: 'banner',
                    target_id: bannerId ? parseInt(bannerId, 10) : null,
                    metadata: {
                        name: sponsorName,
                        destination: sponsorLink.getAttribute('href')
                    }
                });
                return;
            }

            // Video Shorts Play / Click
            const videoCard = e.target.closest('.short-video-card, .video-short-item, [data-track-video]');
            if (videoCard) {
                const videoId = videoCard.getAttribute('data-video-id');
                const videoTitle = videoCard.getAttribute('data-title') || videoCard.querySelector('.short-title')?.textContent?.trim();

                window.trackAnalyticsEvent('video_play', {
                    target_type: 'video_short',
                    target_id: videoId ? parseInt(videoId, 10) : null,
                    metadata: { title: videoTitle }
                });
            }
        });
    });
})();
