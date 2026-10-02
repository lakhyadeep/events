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
            Stat::make('Active Events', "{$activeEventsCount} / {$totalEventsCount}")
                ->description('Published festival microsites')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary')
                ->chart([1, 2, 2, 3, 3, $activeEventsCount]),

            Stat::make('Approved Candidates', $approvedParticipants)
                ->description($pendingParticipants > 0 ? "{$pendingParticipants} pending review" : 'All submissions reviewed')
                ->descriptionIcon($pendingParticipants > 0 ? 'heroicon-m-clock' : 'heroicon-m-check-badge')
                ->color($pendingParticipants > 0 ? 'warning' : 'success')
                ->chart([3, 5, 8, 12, 16, $approvedParticipants]),

            Stat::make('Sponsors & Partners', $activeSponsors)
                ->description("{$presentingCount} Presenting, {$associateCount} Associates")
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('info')
                ->chart([1, 3, 4, 5, 6, $activeSponsors]),

            Stat::make('Awards & Podium', "{$winnersCount} / {$awardsCount}")
                ->description("{$winnersCount} declared winners across categories")
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success')
                ->chart([0, 1, 2, 3, $winnersCount]),
        ];
    }
}
