<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventApiController extends Controller
{
    /**
     * Get full event payload for headless consumption.
     */
    public function show(string $slug): JsonResponse
    {
        $event = Event::with([
            'banners' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'sponsors' => fn ($q) => $q->where('is_active', true)->orderBy('slot_order'),
            'awards' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'videoShorts' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'timelineItems' => fn ($q) => $q->orderBy('sort_order'),
        ])->where('slug', $slug)->firstOrFail();

        $presentingPartner = $event->sponsors->firstWhere('sponsor_type', 'presenting_partner');
        $associateSponsors = $event->sponsors
            ->where('sponsor_type', 'associate_sponsor')
            ->sortBy('slot_order')
            ->take(5)
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'event' => $event,
                'masthead' => [
                    'brand' => 'Dib 24x7',
                    'presenting_partner' => $presentingPartner,
                    'associate_sponsors' => $associateSponsors,
                ],
                'toggles' => [
                    'registration_active' => $event->is_registration_active,
                    'voting_active' => $event->is_voting_active,
                    'awards_active' => $event->is_awards_active,
                    'timeline_active' => $event->is_timeline_active,
                ],
            ],
        ]);
    }

    /**
     * Filterable candidates endpoint.
     */
    public function participants(Request $request, string $slug): JsonResponse
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $participants = Participant::query()
            ->where('event_id', $event->id)
            ->where('registration_status', 'approved')
            ->when($request->query('zone'), fn ($q, $zone) => $q->where('zone', $zone))
            ->when($request->boolean('shortlisted'), fn ($q) => $q->where('is_shortlisted', true))
            ->when($request->query('search'), function ($q, $search) {
                $term = "%{$search}%";
                $q->where(function ($sub) use ($term) {
                    $sub->where('display_name', 'like', $term)
                        ->orWhere('locality', 'like', $term)
                        ->orWhere('puja_theme', 'like', $term)
                        ->orWhere('idol_artist', 'like', $term);
                });
            })
            ->latest('is_shortlisted')
            ->paginate($request->integer('per_page', 12));

        return response()->json([
            'success' => true,
            'data' => $participants,
        ]);
    }

    /**
     * Video shorts endpoint.
     */
    public function shorts(string $slug): JsonResponse
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $shorts = $event->videoShorts()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $shorts,
        ]);
    }

    /**
     * Published winners podium endpoint.
     */
    public function winners(string $slug): JsonResponse
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $winners = $event->winners()
            ->with(['award', 'participant'])
            ->where('status', 'published')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $winners,
        ]);
    }
}
