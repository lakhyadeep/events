<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\View\View;

class EventMicrositeController extends Controller
{
    /**
     * Display the event microsite.
     */
    public function show(?string $slug = null): View
    {
        $query = Event::with([
            'banners' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'sponsors' => fn ($q) => $q->where('is_active', true)->orderBy('slot_order'),
            'awards' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'winners.award',
            'winners.participant',
            'videoShorts' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'timelineItems' => fn ($q) => $q->orderBy('sort_order'),
        ]);

        $event = $slug
            ? $query->where('slug', $slug)->firstOrFail()
            : $query->where('status', 'active')->latest('year')->first();

        if (! $event) {
            abort(404, 'No active event found.');
        }

        // Segregate sponsor tiers for the masthead
        $presentingPartner = $event->sponsors
            ->firstWhere('sponsor_type', 'presenting_partner');

        $associateSponsors = $event->sponsors
            ->where('sponsor_type', 'associate_sponsor')
            ->sortBy('slot_order')
            ->take(5);

        $otherSponsors = $event->sponsors
            ->whereNotIn('sponsor_type', ['presenting_partner', 'associate_sponsor']);

        // Published winners
        $publishedWinners = $event->winners
            ->where('status', 'published');

        return view('frontend.event', compact(
            'event',
            'presentingPartner',
            'associateSponsors',
            'otherSponsors',
            'publishedWinners'
        ));
    }
}
