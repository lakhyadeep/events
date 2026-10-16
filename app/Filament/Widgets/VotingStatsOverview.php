<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\Vote;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VotingStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $data = cache()->remember('filament_voting_stats_overview_data', now()->addSeconds(15), function (): array {
            $totalVotes = Vote::count();
            $uniqueVoters = Vote::distinct('mobile_number')->count('mobile_number');
            $votesToday = Vote::whereDate('created_at', today())->count();
            $activeVotingEvents = Event::where('status', 'active')->where('is_voting_active', true)->count();

            return [
                'totalVotes' => $totalVotes,
                'uniqueVoters' => $uniqueVoters,
                'votesToday' => $votesToday,
                'activeVotingEvents' => $activeVotingEvents,
            ];
        });

        return [
            Stat::make('Total Verified Votes', number_format($data['totalVotes']))
                ->description('OTP-verified vote entries')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('danger')
                ->chart([5, 12, 19, 28, 45, max(1, $data['totalVotes'])]),

            Stat::make('Unique Voters', number_format($data['uniqueVoters']))
                ->description('Distinct verified mobile numbers')
                ->descriptionIcon('heroicon-m-device-phone-mobile')
                ->color('primary')
                ->chart([3, 8, 15, 22, 38, max(1, $data['uniqueVoters'])]),

            Stat::make('Votes Cast Today', number_format($data['votesToday']))
                ->description('Votes recorded in last 24h')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([1, 4, 7, 10, 15, max(1, $data['votesToday'])]),

            Stat::make('Voting Status', $data['activeVotingEvents'] > 0 ? 'Active & Open' : 'Closed')
                ->description($data['activeVotingEvents'] > 0 ? "{$data['activeVotingEvents']} event(s) accepting votes" : 'Voting disabled')
                ->descriptionIcon($data['activeVotingEvents'] > 0 ? 'heroicon-m-signal' : 'heroicon-m-lock-closed')
                ->color($data['activeVotingEvents'] > 0 ? 'success' : 'gray'),
        ];
    }
}
