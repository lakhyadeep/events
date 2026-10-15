<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\SponsorType;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\OtpSetting;
use App\Models\Participant;
use App\Models\Zone;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EventMicrositeController extends Controller
{
    /**
     * Display the event microsite.
     */
    public function show(Event $event): View
    {
        $event->loadMissing([
            'banners' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'sponsors' => fn ($q) => $q->where('is_active', true)->orderBy('slot_order'),
            'awards' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'winners.award',
            'winners.participant' => fn ($q) => $q->withCount('votes')->with(['locality', 'zone']),
            'videoShorts' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'timelineItems' => fn ($q) => $q->orderBy('sort_order'),
        ]);

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
    public function allPujas(Event $event): View
    {
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
                'google_map_url' => $p->google_map_url,
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
     * Strictly accessible only via /vote/{participant}.
     */
    public function vote(Event $event, string $participant): View
    {
        $event->loadMissing([
            'banners' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'sponsors' => fn ($q) => $q->where('is_active', true)->orderBy('slot_order'),
            'awards' => fn ($q) => $q->where('is_active', true)->where('is_voting_allowed', true)->orderBy('sort_order'),
        ]);

        $participants = $event->participants()
            ->approved()
            ->withCount('votes')
            ->with(['zone', 'locality'])
            ->orderByDesc('is_shortlisted')
            ->get();

        $cleanSlug = Str::slug(urldecode($participant));

        $selectedParticipant = $participants->first(fn ($p) => $p->slug === $cleanSlug);

        if (! $selectedParticipant) {
            abort(404);
        }

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
                'google_map_url' => $p->google_map_url,
            ];
        })->values()->toArray();

        $presentingPartner = $event->sponsors
            ->firstWhere('sponsor_type', SponsorType::PresentingPartner);

        $associateSponsors = $event->sponsors
            ->where('sponsor_type', SponsorType::AssociateSponsor)
            ->sortBy('slot_order')
            ->take(5);

        $otherSponsors = $event->sponsors
            ->whereNotIn('sponsor_type', [SponsorType::PresentingPartner, SponsorType::AssociateSponsor]);

        $otpSetting = OtpSetting::current();
        $otpConfig = [
            'length' => (int) $otpSetting->otp_length,
            'resendCooldown' => (int) $otpSetting->resend_cooldown_seconds,
            'expiryMinutes' => (int) $otpSetting->expiry_minutes,
        ];

        return view('frontend.vote', compact(
            'event',
            'participants',
            'pujasData',
            'selectedParticipant',
            'presentingPartner',
            'associateSponsors',
            'otherSponsors',
            'otpConfig'
        ));
    }

    /**
     * Display the Vote Confirmation & Voucher Success page.
     * Strictly accessible only via /vote-success/{participant}.
     */
    public function voteSuccess(Event $event, string $participant): View
    {
        $event->loadMissing([
            'banners' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'sponsors' => fn ($q) => $q->where('is_active', true)->orderBy('slot_order'),
        ]);

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
                'google_map_url' => $p->google_map_url,
            ];
        })->values()->toArray();

        $cleanSlug = Str::slug(urldecode($participant));
        $selectedParticipant = $participants->first(fn ($p) => $p->slug === $cleanSlug);

        if (! $selectedParticipant) {
            abort(404);
        }

        $votedPandal = $selectedParticipant->display_name ?: $selectedParticipant->name;
        $votedCategory = session('vote_success_category');
        $votedMobile = session('vote_success_mobile');

        $presentingPartner = $event->sponsors
            ->firstWhere('sponsor_type', SponsorType::PresentingPartner);

        $associateSponsors = $event->sponsors
            ->where('sponsor_type', SponsorType::AssociateSponsor)
            ->sortBy('slot_order')
            ->take(5);

        $otherSponsors = $event->sponsors
            ->whereNotIn('sponsor_type', [SponsorType::PresentingPartner, SponsorType::AssociateSponsor]);

        $trophySponsor = $event->sponsors->first(
            fn ($s) => str_contains(strtolower($s->title.' '.($s->sponsor_tag ?? '')), 'trophy') ||
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
