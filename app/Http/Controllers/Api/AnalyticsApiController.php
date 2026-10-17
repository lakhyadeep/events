<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class AnalyticsApiController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService
    ) {}

    /**
     * Record a client-side user behavior event.
     */
    public function recordEvent(Request $request): JsonResponse
    {
        if (function_exists('ignore_user_abort')) {
            @ignore_user_abort(true);
        }

        if ($request->isMethod('OPTIONS')) {
            return response()->json([], 204)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Accept');
        }

        // Handle raw payload from navigator.sendBeacon fallback
        $input = $request->all();
        if ((empty($input) || empty($input['action_type'])) && ! empty($request->getContent())) {
            $decoded = json_decode($request->getContent(), true);
            if (is_array($decoded)) {
                $input = array_merge($input, $decoded);
            }
        }

        $validator = validator($input, [
            'action_type' => ['required', 'string', 'max:50'],
            'event_slug' => ['nullable', 'string', 'max:100'],
            'event_id' => ['nullable'],
            'visitor_id' => ['nullable', 'string', 'max:500'],
            'session_id' => ['nullable', 'string', 'max:500'],
            'target_type' => ['nullable', 'string', 'max:50'],
            'target_id' => ['nullable'],
            'metadata' => ['nullable', 'array'],
            'url' => ['nullable', 'string', 'max:500'],
            'device_type' => ['nullable', 'string', 'max:20'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $visitorId = $validated['visitor_id'] ?? $request->cookie('analytics_vid');
        if (! empty($visitorId) && str_starts_with($visitorId, 'eyJ')) {
            try {
                $visitorId = Crypt::decrypt($visitorId, false);
            } catch (\Throwable) {
                // If decrypt fails, continue
            }
        }
        if (empty($visitorId)) {
            $visitorId = (string) Str::uuid();
        }
        $visitorId = Str::limit($visitorId, 64, '');

        $eventId = ! empty($validated['event_id']) ? (int) $validated['event_id'] : null;
        if (! $eventId && ! empty($validated['event_slug'])) {
            $eventId = Event::where('slug', $validated['event_slug'])->value('id');
        }

        // Fallback: resolve event from URL or active event so events are never orphaned
        if (! $eventId) {
            $url = $validated['url'] ?? $request->header('referer') ?? '';
            $path = parse_url($url, PHP_URL_PATH) ?? '';
            if (preg_match('#^/([a-zA-Z0-9_-]+)(?:/|$)#', $path, $matches)) {
                $possibleSlug = $matches[1];
                if (! in_array($possibleSlug, ['api', 'admin', 'admin-tools', 'all-pujas', 'storage', 'event-assets', 'livewire'])) {
                    $eventId = Event::where('slug', $possibleSlug)->value('id');
                }
            }

            if (! $eventId) {
                $eventId = Event::where('status', 'active')->latest('year')->value('id');
            }
        }

        $targetId = ! empty($validated['target_id']) ? (int) $validated['target_id'] : null;
        $deviceType = $validated['device_type'] ?? $this->analyticsService->detectDeviceType($request->userAgent());

        $activity = $this->analyticsService->recordActivity(
            actionType: $validated['action_type'],
            visitorId: $visitorId,
            sessionId: $validated['session_id'] ?? null,
            eventId: $eventId,
            targetType: $validated['target_type'] ?? null,
            targetId: $targetId,
            metadata: $validated['metadata'] ?? [],
            url: $validated['url'] ?? $request->header('referer'),
            deviceType: $deviceType
        );

        return response()->json([
            'success' => true,
            'id' => $activity->id,
            'visitor_id' => $visitorId,
        ], 201)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Accept');
    }
}
