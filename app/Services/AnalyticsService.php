<?php

namespace App\Services;

use App\Models\Event;
use App\Models\PageView;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnalyticsService
{
    /**
     * Record an incoming page view request.
     */
    public function recordPageView(Request $request, string $visitorId, ?string $sessionId = null): ?PageView
    {
        $userAgent = $request->userAgent() ?? '';

        if ($this->isBot($userAgent) || $this->shouldIgnorePath($request->path())) {
            return null;
        }

        $event = $this->resolveEventFromRequest($request);
        $pageType = $this->resolvePageType($request);
        $deviceType = $this->detectDeviceType($userAgent);
        $browser = $this->detectBrowser($userAgent);
        $platform = $this->detectPlatform($userAgent);
        $referrer = $request->header('referer');
        $referrerHost = $referrer ? parse_url($referrer, PHP_URL_HOST) : null;
        $ip = $this->anonymizeIp($request->ip());

        return PageView::create([
            'event_id' => $event?->id,
            'visitor_id' => $visitorId,
            'session_id' => $sessionId ?? ($request->hasSession() ? $request->session()->getId() : null),
            'page_type' => $pageType,
            'url' => Str::limit($request->fullUrl(), 500, ''),
            'route_name' => $request->route()?->getName(),
            'referrer_host' => $referrerHost ? Str::limit(strtolower($referrerHost), 150, '') : null,
            'referrer' => $referrer,
            'device_type' => $deviceType,
            'browser' => $browser,
            'platform' => $platform,
            'ip_address' => $ip,
            'user_agent' => Str::limit($userAgent, 500, ''),
        ]);
    }

    /**
     * Record a user interaction/event (search, click, etc.).
     */
    public function recordActivity(
        string $actionType,
        string $visitorId,
        ?string $sessionId = null,
        ?int $eventId = null,
        ?string $targetType = null,
        ?int $targetId = null,
        array $metadata = [],
        ?string $url = null,
        ?string $deviceType = null
    ): UserActivity {
        return UserActivity::create([
            'event_id' => $eventId,
            'visitor_id' => $visitorId,
            'session_id' => $sessionId,
            'action_type' => Str::limit($actionType, 50, ''),
            'target_type' => $targetType ? Str::limit($targetType, 50, '') : null,
            'target_id' => $targetId,
            'url' => $url ? Str::limit($url, 500, '') : null,
            'metadata' => ! empty($metadata) ? $metadata : null,
            'device_type' => $deviceType ?? 'mobile',
        ]);
    }

    /**
     * Check if path should be ignored (admin, assets, livewire internal, healthcheck).
     */
    public function shouldIgnorePath(string $path): bool
    {
        $normalized = trim($path, '/');

        $ignoredPrefixes = [
            'admin',
            'admin-tools',
            'livewire',
            'up',
            'api/analytics',
            'event-assets',
            'storage',
            'telescope',
            'horizon',
            'filament',
        ];

        foreach ($ignoredPrefixes as $prefix) {
            if ($normalized === $prefix || str_starts_with($normalized, $prefix.'/') || str_starts_with($normalized, $prefix.'-')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detect known bots, crawlers, and scrapers.
     */
    public function isBot(?string $userAgent): bool
    {
        if (empty($userAgent)) {
            return false;
        }

        $botPatterns = [
            'bot', 'crawl', 'slurp', 'spider', 'mediapartners', 'googlebot',
            'bingbot', 'yandex', 'baidu', 'duckduckbot', 'facebot', 'facebookexternalhit',
            'twitterbot', 'rogerbot', 'linkedinbot', 'embedly', 'quora link preview',
            'showyoubot', 'outbrain', 'pinterest', 'slackbot', 'vkshare', 'w3c_validator',
            'screaming frog', 'ahrefs', 'semrush', 'mj12bot', 'dotbot', 'uptime',
            'curl', 'python-requests', 'guzzle', 'postman', 'headlesschrome',
        ];

        $lower = strtolower($userAgent);

        foreach ($botPatterns as $pattern) {
            if (str_contains($lower, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Resolve the Event model from the current request route or slug.
     */
    public function resolveEventFromRequest(Request $request): ?Event
    {
        $eventParam = $request->route('event');

        if ($eventParam instanceof Event) {
            return $eventParam;
        }

        if (is_string($eventParam) && ! empty($eventParam)) {
            return Event::where('slug', $eventParam)->first();
        }

        // Check if there is an active event for root requests
        return Event::where('status', 'active')->latest('year')->first();
    }

    /**
     * Determine page type classification.
     */
    public function resolvePageType(Request $request): string
    {
        $routeName = $request->route()?->getName();

        if ($routeName) {
            return match ($routeName) {
                'home', 'welcome', 'events.show' => 'homepage',
                'events.all-pujas', 'all-pujas.redirect' => 'all_puja',
                'events.vote' => 'vote',
                'events.vote-success' => 'vote_success',
                default => 'other',
            };
        }

        $path = trim($request->path(), '/');
        if (empty($path)) {
            return 'homepage';
        }

        if (str_contains($path, 'vote-success')) {
            return 'vote_success';
        }

        if (str_contains($path, 'vote')) {
            return 'vote';
        }

        if (str_contains($path, 'all-pujas')) {
            return 'all_puja';
        }

        return 'homepage';
    }

    /**
     * Detect device type (mobile, tablet, desktop).
     */
    public function detectDeviceType(?string $userAgent): string
    {
        if (empty($userAgent)) {
            return 'desktop';
        }

        $ua = strtolower($userAgent);

        if (str_contains($ua, 'ipad') || (str_contains($ua, 'android') && ! str_contains($ua, 'mobile')) || str_contains($ua, 'tablet')) {
            return 'tablet';
        }

        if (str_contains($ua, 'mobile') || str_contains($ua, 'iphone') || str_contains($ua, 'ipod') || str_contains($ua, 'android') || str_contains($ua, 'webos') || str_contains($ua, 'blackberry')) {
            return 'mobile';
        }

        return 'desktop';
    }

    /**
     * Detect browser name.
     */
    public function detectBrowser(?string $userAgent): string
    {
        if (empty($userAgent)) {
            return 'Unknown';
        }

        $ua = strtolower($userAgent);

        if (str_contains($ua, 'edg/')) {
            return 'Edge';
        }
        if (str_contains($ua, 'samsungbrowser/')) {
            return 'Samsung Browser';
        }
        if (str_contains($ua, 'opr/') || str_contains($ua, 'opera')) {
            return 'Opera';
        }
        if (str_contains($ua, 'chrome/') || str_contains($ua, 'crios/')) {
            return 'Chrome';
        }
        if (str_contains($ua, 'firefox/') || str_contains($ua, 'fxios/')) {
            return 'Firefox';
        }
        if (str_contains($ua, 'safari/') && ! str_contains($ua, 'chrome/')) {
            return 'Safari';
        }

        return 'Other';
    }

    /**
     * Detect Operating System platform.
     */
    public function detectPlatform(?string $userAgent): string
    {
        if (empty($userAgent)) {
            return 'Unknown';
        }

        $ua = strtolower($userAgent);

        if (str_contains($ua, 'android')) {
            return 'Android';
        }
        if (str_contains($ua, 'iphone') || str_contains($ua, 'ipad') || str_contains($ua, 'ipod')) {
            return 'iOS';
        }
        if (str_contains($ua, 'windows')) {
            return 'Windows';
        }
        if (str_contains($ua, 'macintosh') || str_contains($ua, 'mac os x')) {
            return 'macOS';
        }
        if (str_contains($ua, 'linux')) {
            return 'Linux';
        }

        return 'Other';
    }

    /**
     * Mask IP address for privacy compliance.
     */
    public function anonymizeIp(?string $ip): ?string
    {
        if (empty($ip)) {
            return null;
        }

        // IPv4: mask last octet: 192.168.1.123 -> 192.168.1.0
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return preg_replace('/\.\d+$/', '.0', $ip);
        }

        // IPv6: mask host segment: 2001:db8:85a3:0000:0000:8a2e:0370:7334 -> 2001:db8:85a3::
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $parts = explode(':', $ip);
            if (count($parts) >= 3) {
                return $parts[0].':'.$parts[1].':'.$parts[2].'::';
            }
        }

        return null;
    }
}
