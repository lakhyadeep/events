<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\SponsorType;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
            'winners.participant' => fn ($q) => $q->withCount('votes')->with(['locality', 'zone']),
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
            ->withCount('votes')
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
            ->withCount('votes')
            ->with(['zone', 'locality'])
            ->orderByDesc('is_shortlisted')
            ->get();

        $zones = Zone::active()->orderBy('sort_order')->get();

        $event->loadMissing([
            'banners' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'sponsors' => fn ($q) => $q->where('is_active', true)->orderBy('slot_order'),
            'awards' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'winners.award',
            'winners.participant' => fn ($q) => $q->withCount('votes')->with(['locality', 'zone']),
            'videoShorts' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'timelineItems' => fn ($q) => $q->orderBy('sort_order'),
        ]);

        $publishedWinners = $event->winners->where('status', 'published');

        // Transform participants into directory schema
        $pujasData = $participants->map(function ($p) {
            $zoneName = $p->zone?->name ?? ($p->getRawOriginal('zone') ?: '');
            $localityName = $p->locality?->name ?? ($p->getRawOriginal('locality') ?: '');

            return [
                'id' => $p->id,
                'slug' => $p->slug,
                'name' => $p->display_name ?: $p->name,
                'location' => trim(($localityName ? $localityName.($zoneName ? ', ' : '') : '').$zoneName),
                'zone' => $zoneName,
                'category' => $p->is_shortlisted ? 'Best Puja' : 'General Entry',
                'categoryPill' => $p->is_shortlisted ? 'Flagship Award' : 'Community Puja',
                'categoryClass' => $p->is_shortlisted ? 'tag-best-puja' : 'tag-general',
                'votes' => (int) ($p->votes_count ?? 0),
                'established' => $p->first_year_of_puja ? 'Est. '.$p->first_year_of_puja : '',
                'theme' => $p->puja_theme ?: ($p->short_introduction ?: ''),
                'image' => $p->primary_image_url ?: asset('event-assets/images/hero.png'),
            ];
        })->values()->toArray();

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
            'publishedWinners',
            'zones',
            'pujasData',
            'totalPujasCount',
            'filterZones'
        ));
    }

    /**
     * Display the dedicated Voting page (v2 flow).
     */
    public function vote(Request $request, ?string $slug = null, ?string $participant = null): View|RedirectResponse
    {
        $hasEventSlugInRoute = $request->route() ? $request->route()->hasParameter('slug') : false;
        $routeSlug = $hasEventSlugInRoute ? $slug : null;
        $routeParticipant = $hasEventSlugInRoute ? $participant : ($participant ?: $slug);

        $query = Event::with([
            'banners' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'sponsors' => fn ($q) => $q->where('is_active', true)->orderBy('slot_order'),
            'awards' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
        ]);

        $event = $routeSlug
            ? $query->where('slug', $routeSlug)->first()
            : $query->where('status', 'active')->latest('year')->first();

        if (! $event) {
            $event = $query->latest('year')->latest('id')->first();
        }

        if (! $event) {
            return view('frontend.no-events');
        }

        $participants = $event->participants()
            ->approved()
            ->withCount('votes')
            ->with(['zone', 'locality'])
            ->orderByDesc('is_shortlisted')
            ->get();

        $pujasData = $participants->map(function ($p) {
            $zoneName = $p->zone?->name ?? ($p->getRawOriginal('zone') ?: '');
            $localityName = $p->locality?->name ?? ($p->getRawOriginal('locality') ?: '');

            return [
                'id' => $p->id,
                'slug' => $p->slug,
                'name' => $p->display_name ?: $p->name,
                'location' => trim(($localityName ? $localityName.($zoneName ? ', ' : '') : '').$zoneName),
                'zone' => $zoneName,
                'votes' => (int) ($p->votes_count ?? 0),
                'theme' => $p->puja_theme ?: ($p->short_introduction ?: ''),
                'image' => $p->primary_image_url ?: '',
            ];
        })->values()->toArray();

        $selectedParticipant = null;

        // 1. Resolve from route path parameter if present (/vote/{participant} or /events/{slug}/vote/{participant})
        if ($routeParticipant) {
            $decoded = urldecode((string) $routeParticipant);
            $cleanSlug = Str::slug($decoded);

            // Match by exact accessor slug
            $selectedParticipant = $participants->first(fn ($p) => $p->slug === $cleanSlug || $p->slug === $decoded);

            // Match by numeric ID if parameter is numeric or prefixed with ID
            if (! $selectedParticipant && (is_numeric($decoded) || preg_match('/^(\d+)/', $decoded, $matches))) {
                $candidateId = is_numeric($decoded) ? (int) $decoded : (int) $matches[1];
                $selectedParticipant = $participants->firstWhere('id', $candidateId);
            }

            // Fallback match by committee name
            if (! $selectedParticipant) {
                $cleanName = mb_strtolower(trim($decoded));
                $selectedParticipant = $participants->first(fn ($p) => mb_strtolower($p->display_name ?: $p->name) === $cleanName)
                    ?? $participants->first(fn ($p) => Str::slug($p->name) === $cleanSlug);
            }

            if (! $selectedParticipant) {
                abort(404);
            }

            // If accessed via numeric ID only (e.g. /vote/31), 301 redirect to canonical slug URL
            if (is_numeric($decoded) && $selectedParticipant->slug) {
                $redirectUrl = $routeSlug
                    ? route('events.vote.participant', array_merge(['slug' => $event->slug, 'participant' => $selectedParticipant->slug], $request->query()))
                    : route('vote.participant', array_merge(['participant' => $selectedParticipant->slug], $request->query()));

                return redirect()->to($redirectUrl, 301);
            }
        }

        // 2. Handle legacy query parameters (e.g. ?pandal=...&participant=31) with a 301 Permanent Redirect
        $hasLegacyQuery = $request->has('pandal')
            || $request->has('committee')
            || $request->has('participant')
            || $request->has('participant_id');

        if ($hasLegacyQuery) {
            $selectedParticipantId = $request->query('participant') ?: $request->query('participant_id');
            $selectedPandalName = $request->query('pandal') ?: $request->query('committee');

            $queryMatched = null;
            if ($selectedParticipantId) {
                $queryMatched = $participants->firstWhere('id', (int) $selectedParticipantId);
            }

            if (! $queryMatched && $selectedPandalName) {
                $clean = mb_strtolower(trim((string) $selectedPandalName));
                $queryMatched = $participants->first(fn ($p) => mb_strtolower($p->display_name ?: $p->name) === $clean)
                    ?? $participants->first(fn ($p) => str_contains(mb_strtolower($p->display_name ?: $p->name), $clean))
                    ?? $participants->first(fn ($p) => Str::slug($p->display_name ?: $p->name) === Str::slug($clean));
            }

            if ($queryMatched && $queryMatched->slug) {
                $extraParams = $request->except(['pandal', 'committee', 'participant', 'participant_id']);
                $redirectUrl = $routeSlug
                    ? route('events.vote.participant', array_merge(['slug' => $event->slug, 'participant' => $queryMatched->slug], $extraParams))
                    : route('vote.participant', array_merge(['participant' => $queryMatched->slug], $extraParams));

                return redirect()->to($redirectUrl, 301);
            }

            if ($queryMatched) {
                $selectedParticipant = $queryMatched;
            }
        }

        if (! $selectedParticipant && $participants->isNotEmpty()) {
            $selectedParticipant = $participants->first();
        }

        $presentingPartner = $event->sponsors
            ->firstWhere('sponsor_type', SponsorType::PresentingPartner);

        $associateSponsors = $event->sponsors
            ->where('sponsor_type', SponsorType::AssociateSponsor)
            ->sortBy('slot_order')
            ->take(5);

        $otherSponsors = $event->sponsors
            ->whereNotIn('sponsor_type', [SponsorType::PresentingPartner, SponsorType::AssociateSponsor]);

        return view('frontend.vote', compact(
            'event',
            'participants',
            'pujasData',
            'selectedParticipant',
            'presentingPartner',
            'associateSponsors',
            'otherSponsors'
        ));
    }

    /**
     * Display the Vote Confirmation & Voucher Success page (v2 flow).
     */
    public function voteSuccess(Request $request, ?string $slug = null, ?string $participant = null): View|RedirectResponse
    {
        $hasEventSlugInRoute = $request->route() ? $request->route()->hasParameter('slug') : false;
        $routeSlug = $hasEventSlugInRoute ? $slug : null;
        $routeParticipant = $hasEventSlugInRoute ? $participant : ($participant ?: $slug);

        $query = Event::with([
            'banners' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'sponsors' => fn ($q) => $q->where('is_active', true)->orderBy('slot_order'),
        ]);

        $event = $routeSlug
            ? $query->where('slug', $routeSlug)->first()
            : $query->where('status', 'active')->latest('year')->first();

        if (! $event) {
            $event = $query->latest('year')->latest('id')->first();
        }

        if (! $event) {
            return view('frontend.no-events');
        }

        $participants = $event->participants()
            ->approved()
            ->withCount('votes')
            ->with(['zone', 'locality'])
            ->get();

        $pujasData = $participants->map(function ($p) {
            $zoneName = $p->zone?->name ?? ($p->getRawOriginal('zone') ?: '');
            $localityName = $p->locality?->name ?? ($p->getRawOriginal('locality') ?: '');

            return [
                'id' => $p->id,
                'slug' => $p->slug,
                'name' => $p->display_name ?: $p->name,
                'location' => trim(($localityName ? $localityName.($zoneName ? ', ' : '') : '').$zoneName),
                'zone' => $zoneName,
                'votes' => (int) ($p->votes_count ?? 0),
                'theme' => $p->puja_theme ?: ($p->short_introduction ?: ''),
                'image' => $p->primary_image_url ?: '',
            ];
        })->values()->toArray();

        // 1. Resolve participant from route parameter if present (/vote-success/{participant})
        $selectedParticipant = null;
        if ($routeParticipant) {
            $decoded = urldecode((string) $routeParticipant);
            $cleanSlug = Str::slug($decoded);

            $selectedParticipant = $participants->first(fn ($p) => $p->slug === $cleanSlug || $p->slug === $decoded);

            if (! $selectedParticipant && (is_numeric($decoded) || preg_match('/^(\d+)/', $decoded, $matches))) {
                $candidateId = is_numeric($decoded) ? (int) $decoded : (int) $matches[1];
                $selectedParticipant = $participants->firstWhere('id', $candidateId);
            }

            if (! $selectedParticipant) {
                $cleanName = mb_strtolower(trim($decoded));
                $selectedParticipant = $participants->first(fn ($p) => mb_strtolower($p->display_name ?: $p->name) === $cleanName)
                    ?? $participants->first(fn ($p) => Str::slug($p->name) === $cleanSlug);
            }
        }

        // 2. Intercept legacy query string: ?pandal=...&category=...&mobile=...
        $hasLegacyQuery = $request->has('pandal')
            || $request->has('committee')
            || $request->has('participant')
            || $request->has('participant_id')
            || $request->has('category')
            || $request->has('feature')
            || $request->has('mobile');

        if ($hasLegacyQuery) {
            $selectedParticipantId = $request->query('participant') ?: $request->query('participant_id');
            $selectedPandalName = $request->query('pandal') ?: $request->query('committee');
            $categoryParam = $request->query('category') ?: $request->query('feature');
            $mobileParam = $request->query('mobile');

            $matchedParticipant = null;
            if ($selectedParticipantId) {
                $matchedParticipant = $participants->firstWhere('id', (int) $selectedParticipantId);
            }
            if (! $matchedParticipant && $selectedPandalName) {
                $clean = mb_strtolower(trim((string) $selectedPandalName));
                $matchedParticipant = $participants->first(fn ($p) => mb_strtolower($p->display_name ?: $p->name) === $clean)
                    ?? $participants->first(fn ($p) => str_contains(mb_strtolower($p->display_name ?: $p->name), $clean))
                    ?? $participants->first(fn ($p) => Str::slug($p->display_name ?: $p->name) === Str::slug($clean));
            }

            // Flash details into session so the success page has access without exposing query params in URL
            if ($matchedParticipant) {
                session()->flash('vote_success_pandal', $matchedParticipant->display_name ?: $matchedParticipant->name);
                session()->flash('vote_success_slug', $matchedParticipant->slug);
            } elseif ($selectedPandalName) {
                session()->flash('vote_success_pandal', $selectedPandalName);
            }

            if ($categoryParam) {
                session()->flash('vote_success_category', $categoryParam);
            }
            if ($mobileParam) {
                session()->flash('vote_success_mobile', $mobileParam);
            }

            // 301 Permanent Redirect to clean SEO URL (stripping out ugly parameters & sensitive mobile numbers!)
            $redirectTargetSlug = $matchedParticipant?->slug ?: ($selectedParticipant?->slug ?? null);

            if ($redirectTargetSlug) {
                $cleanUrl = $routeSlug
                    ? route('events.vote-success.participant', ['slug' => $event->slug, 'participant' => $redirectTargetSlug])
                    : route('vote-success.participant', ['participant' => $redirectTargetSlug]);
            } else {
                $cleanUrl = $routeSlug
                    ? route('events.vote-success', $event->slug)
                    : route('vote-success');
            }

            return redirect()->to($cleanUrl, 301);
        }

        // Retrieve flashed session values if present
        $votedPandal = $selectedParticipant ? ($selectedParticipant->display_name ?: $selectedParticipant->name) : session('vote_success_pandal');
        $votedCategory = session('vote_success_category') ?: $request->query('category');
        $votedMobile = session('vote_success_mobile') ?: $request->query('mobile');

        $presentingPartner = $event->sponsors
            ->firstWhere('sponsor_type', SponsorType::PresentingPartner);

        $associateSponsors = $event->sponsors
            ->where('sponsor_type', SponsorType::AssociateSponsor)
            ->sortBy('slot_order')
            ->take(5);

        $otherSponsors = $event->sponsors
            ->whereNotIn('sponsor_type', [SponsorType::PresentingPartner, SponsorType::AssociateSponsor]);

        $trophySponsor = $event->sponsors->first(fn ($s) => str_contains(strtolower($s->title.' '.($s->sponsor_tag ?? '')), 'trophy') ||
            str_contains(strtolower($s->title), 'toni')
        );

        return view('frontend.vote-success', compact(
            'event',
            'participants',
            'pujasData',
            'selectedParticipant',
            'votedPandal',
            'votedCategory',
            'votedMobile',
            'presentingPartner',
            'associateSponsors',
            'otherSponsors',
            'trophySponsor'
        ));
    }
}
