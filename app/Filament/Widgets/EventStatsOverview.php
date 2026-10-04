<?php

namespace App\Filament\Widgets;

use App\Models\Award;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Sponsor;
use App\Models\Winner;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EventStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $data = cache()->remember('filament_event_stats_overview_data', now()->addSeconds(30), function (): array {
            $activeEventsCount = Event::where('status', 'active')->count();
            $totalEventsCount = Event::count();

            $approvedParticipants = Participant::where('registration_status', 'approved')->count();
            $pendingParticipants = Participant::where('registration_status', 'pending')->count();

            $activeSponsors = Sponsor::where('is_active', true)->count();
            $presentingCount = Sponsor::where('is_active', true)->where('sponsor_type', 'presenting_partner')->count();
            $associateCount = Sponsor::where('is_active', true)->where('sponsor_type', 'associate_sponsor')->count();

            $awardsCount = Award::where('is_active', true)->count();
            $winnersCount = Winner::where('status', 'published')->count();

            return [
                'activeEventsCount' => $activeEventsCount,
                'totalEventsCount' => $totalEventsCount,
                'approvedParticipants' => $approvedParticipants,
                'pendingParticipants' => $pendingParticipants,
                'activeSponsors' => $activeSponsors,
                'presentingCount' => $presentingCount,
                'associateCount' => $associateCount,
                'awardsCount' => $awardsCount,
                'winnersCount' => $winnersCount,
            ];
        });

        return [
            Stat::make('Active Events', "{$data['activeEventsCount']} / {$data['totalEventsCount']}")
                ->description('Published festival microsites')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary')
                ->chart([1, 2, 2, 3, 3, $data['activeEventsCount']]),

            Stat::make('Approved Candidates', $data['approvedParticipants'])
                ->description($data['pendingParticipants'] > 0 ? "{$data['pendingParticipants']} pending review" : 'All submissions reviewed')
                ->descriptionIcon($data['pendingParticipants'] > 0 ? 'heroicon-m-clock' : 'heroicon-m-check-badge')
                ->color($data['pendingParticipants'] > 0 ? 'warning' : 'success')
                ->chart([3, 5, 8, 12, 16, $data['approvedParticipants']]),

            Stat::make('Sponsors & Partners', $data['activeSponsors'])
                ->description("{$data['presentingCount']} Presenting, {$data['associateCount']} Associates")
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('info')
                ->chart([1, 3, 4, 5, 6, $data['activeSponsors']]),

            Stat::make('Awards & Podium', "{$data['winnersCount']} / {$data['awardsCount']}")
                ->description("{$data['winnersCount']} declared winners across categories")
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success')
                ->chart([0, 1, 2, 3, $data['winnersCount']]),
        ];
    }
}
