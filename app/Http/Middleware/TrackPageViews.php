<?php

namespace App\Http\Middleware;

use App\Services\AnalyticsService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TrackPageViews
{
    public function __construct(
        protected AnalyticsService $analyticsService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Fail-safe killswitch
        if (! config('analytics.enabled', env('ANALYTICS_ENABLED', true))) {
            return $next($request);
        }

        try {
            $visitorId = $request->cookie('analytics_vid');
            $isNewVisitor = false;

            if (empty($visitorId)) {
                $visitorId = (string) Str::uuid();
                $isNewVisitor = true;
            }

            $request->attributes->set('analytics_visitor_id', $visitorId);

            /** @var Response $response */
            $response = $next($request);

            if ($isNewVisitor) {
                // Queue cookie for 1 year (525600 minutes)
                $response->headers->setCookie(
                    cookie(
                        name: 'analytics_vid',
                        value: $visitorId,
                        minutes: 525600,
                        path: '/',
                        domain: null,
                        secure: $request->isSecure(),
                        httpOnly: false,
                        raw: false,
                        sameSite: 'lax'
                    )
                );
            }

            return $response;
        } catch (Throwable) {
            // Under zero circumstances allow analytics cookie logic to interrupt request
            return $next($request);
        }
    }

    /**
     * Terminate request lifecycle: perform DB write after response is sent to browser.
     */
    public function terminate(Request $request, Response $response): void
    {
        // Fail-safe killswitch
        if (! config('analytics.enabled', env('ANALYTICS_ENABLED', true))) {
            return;
        }

        // Only track successful GET document visits (200 OK), ignoring redirects, non-GET, AJAX, and Livewire internal requests
        if (! $request->isMethod('GET') || $request->ajax() || $request->hasHeader('X-Livewire') || $response->getStatusCode() !== 200) {
            return;
        }

        try {
            $visitorId = $request->attributes->get('analytics_visitor_id') ?? $request->cookie('analytics_vid');

            if (! empty($visitorId)) {
                $this->analyticsService->recordPageView(
                    request: $request,
                    visitorId: $visitorId,
                    sessionId: $request->hasSession() ? $request->session()->getId() : null
                );
            }
        } catch (Throwable) {
            // Completely silent: database failures during tracking must never affect the user
        }
    }
}
