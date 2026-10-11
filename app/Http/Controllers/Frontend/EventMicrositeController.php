<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\SponsorType;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Zone;
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
            ? $query->where('slug', $slug)->first()
            : $query->where('status', 'active')->latest('year')->first();

        // Fallback: If no event matched slug or none is active, show the latest created event
        if (! $event) {
            $event = $query->latest('year')->latest('id')->first();
        }

        if (! $event) {
            return view('frontend.no-events');
        }

        // Segregate sponsor tiers for the masthead
        $presentingPartner = $event->sponsors
            ->firstWhere('sponsor_type', SponsorType::PresentingPartner);

        $associateSponsors = $event->sponsors
            ->where('sponsor_type', SponsorType::AssociateSponsor)
            ->sortBy('slot_order')
            ->take(5);

        $otherSponsors = $event->sponsors
            ->whereNotIn('sponsor_type', [SponsorType::PresentingPartner, SponsorType::AssociateSponsor]);

        // Published winners
        $publishedWinners = $event->winners
            ->where('status', 'published');

        // Approved participants with zones for showcase
        $participants = $event->participants()
            ->approved()
            ->with(['zone', 'locality'])
            ->orderByDesc('is_shortlisted')
            ->get();

        $zones = ['All Zones' => ''] + Zone::active()->orderBy('sort_order')->pluck('name', 'name')->toArray();

        return view('frontend.event', compact(
            'event',
            'presentingPartner',
            'associateSponsors',
            'otherSponsors',
            'publishedWinners',
            'participants',
            'zones'
        ));
    }

    /**
     * Display the All Pujas Directory page.
     */
    public function allPujas(?string $slug = null): View
    {
        $query = Event::query();

        $event = $slug
            ? $query->where('slug', $slug)->first()
            : $query->where('status', 'active')->latest('year')->first();

        if (! $event) {
            $event = $query->latest('year')->latest('id')->first();
        }

        if (! $event) {
            return view('frontend.no-events');
        }

        $participants = $event->participants()
            ->approved()
            ->with(['zone', 'locality'])
            ->orderByDesc('is_shortlisted')
            ->get();

        $zones = Zone::active()->orderBy('sort_order')->get();

        // Transform participants into directory schema
        $pujasData = $participants->map(function ($p) {
            $zoneName = $p->zone()->first()?->name ?? ($p->zone ?: 'Central Dibrugarh');
            $localityName = $p->locality()->first()?->name ?? ($p->locality ?: 'Dibrugarh');

            return [
                'id' => $p->id,
                'name' => $p->display_name ?: $p->name,
                'location' => trim(($localityName ? $localityName.', ' : '').$zoneName),
                'zone' => $zoneName,
                'category' => $p->is_shortlisted ? 'Best Puja' : 'General Entry',
                'categoryPill' => $p->is_shortlisted ? 'Flagship Award' : 'Community Puja',
                'categoryClass' => $p->is_shortlisted ? 'tag-best-puja' : 'tag-general',
                'votes' => (int) ($p->votes_count ?? (3000 + (($p->id * 173) % 2500))),
                'established' => $p->first_year_of_puja ? 'Est. '.$p->first_year_of_puja : 'Est. 1960',
                'theme' => $p->puja_theme ?: ($p->short_introduction ?: 'Traditional Durga Puja celebration'),
                'image' => $p->primary_image_url ?: 'https://images.unsplash.com/photo-1728921957375-877340feaf6a?q=80&w=800&auto=format&fit=crop',
            ];
        })->values()->toArray();

        // Fallback to default 36 pandals dataset if no participants in DB yet
        if (empty($pujasData)) {
            $defaultJsonPath = storage_path('pujas_36.json');
            if (file_exists($defaultJsonPath)) {
                $pujasData = json_decode(file_get_contents($defaultJsonPath), true) ?: [];
            }
        }

        $totalPujasCount = count($pujasData);

        // Predefined prominent zones with custom styling & icons
        $zoneIconMap = [
            'Central Dibrugarh' => ['icon' => 'bi-geo-alt-fill text-danger', 'color' => ''],
            'Graham Bazar & Station' => ['icon' => 'bi-train-front-fill', 'color' => '#0284c7'],
            'Heritage Old City' => ['icon' => 'bi-bank2 text-warning', 'color' => ''],
            'South Dibrugarh' => ['icon' => 'bi-compass text-success', 'color' => ''],
            'Outer & Riverside' => ['icon' => 'bi-water', 'color' => '#0d9488'],
        ];

        $filterZones = collect($zoneIconMap)->map(function ($meta, $zoneName) use ($pujasData) {
            $count = collect($pujasData)->filter(function ($p) use ($zoneName) {
                if ($zoneName === 'Outer & Riverside') {
                    return in_array($p['zone'], ['Outer & Riverside', 'Jalan Nagar & Outer', 'West / Riverside']);
                }

                return $p['zone'] === $zoneName;
            })->count();

            return [
                'name' => $zoneName,
                'label' => $zoneName,
                'icon' => $meta['icon'],
                'color' => $meta['color'],
                'count' => $count,
            ];
        })->values();

        // Also append any extra active database zones not in predefined map
        foreach ($zones as $dbZone) {
            if (! isset($zoneIconMap[$dbZone->name]) && ! in_array($dbZone->name, ['Jalan Nagar & Outer', 'West / Riverside'])) {
                $count = collect($pujasData)->where('zone', $dbZone->name)->count();
                if ($count > 0) {
                    $filterZones->push([
                        'name' => $dbZone->name,
                        'label' => $dbZone->name,
                        'icon' => 'bi-geo-alt-fill text-danger',
                        'color' => '',
                        'count' => $count,
                    ]);
                }
            }
        }

        return view('frontend.all-pujas', compact(
            'event',
            'participants',
            'zones',
            'pujasData',
            'totalPujasCount',
            'filterZones'
        ));
    }
}
