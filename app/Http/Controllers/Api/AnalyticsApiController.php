<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $validated = $request->validate([
            'action_type' => ['required', 'string', 'max:50'],
            'event_slug' => ['nullable', 'string', 'max:100'],
            'event_id' => ['nullable', 'integer'],
            'visitor_id' => ['nullable', 'string', 'max:64'],
            'session_id' => ['nullable', 'string', 'max:64'],
            'target_type' => ['nullable', 'string', 'max:50'],
            'target_id' => ['nullable', 'integer'],
            'metadata' => ['nullable', 'array'],
            'url' => ['nullable', 'string', 'max:500'],
            'device_type' => ['nullable', 'string', 'max:20'],
        ]);

        $visitorId = $validated['visitor_id'] ?? $request->cookie('analytics_vid');

        if (empty($visitorId)) {
            $visitorId = (string) Str::uuid();
        }

        $eventId = $validated['event_id'] ?? null;
        if (! $eventId && ! empty($validated['event_slug'])) {
            $eventId = Event::where('slug', $validated['event_slug'])->value('id');
        }

        $deviceType = $validated['device_type'] ?? $this->analyticsService->detectDeviceType($request->userAgent());

        $activity = $this->analyticsService->recordActivity(
            actionType: $validated['action_type'],
            visitorId: $visitorId,
            sessionId: $validated['session_id'] ?? null,
            eventId: $eventId,
            targetType: $validated['target_type'] ?? null,
            targetId: $validated['target_id'] ?? null,
            metadata: $validated['metadata'] ?? [],
            url: $validated['url'] ?? $request->header('referer'),
            deviceType: $deviceType
        );

        return response()->json([
            'success' => true,
            'id' => $activity->id,
            'visitor_id' => $visitorId,
        ], 201);
    }
}
