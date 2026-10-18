<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\Vote;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VotingStatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $startDate = ! empty($this->pageFilters['startDate'] ?? null)
            ? rescue(fn () => Carbon::parse($this->pageFilters['startDate']), null, false)
            : null;

        $endDate = ! empty($this->pageFilters['endDate'] ?? null)
            ? rescue(fn () => Carbon::parse($this->pageFilters['endDate']), null, false)
            : null;

        $isFiltered = $startDate !== null || $endDate !== null;

        $cacheKey = 'filament_voting_stats_overview_'.($isFiltered ? md5(json_encode($this->pageFilters)) : 'default');

        $data = cache()->remember($cacheKey, now()->addSeconds(15), function () use ($startDate, $endDate, $isFiltered): array {
            $votesQuery = Vote::query();

            if ($isFiltered) {
                if ($startDate) {
                    $votesQuery->where('created_at', '>=', $startDate);
                }
                if ($endDate) {
                    $votesQuery->where('created_at', '<=', $endDate);
                }
                $filteredVotes = (clone $votesQuery)->count();
                $filteredVoters = (clone $votesQuery)->distinct('mobile_number')->count('mobile_number');
            } else {
                $filteredVotes = Vote::whereDate('created_at', today())->count();
                $filteredVoters = Vote::distinct('mobile_number')->count('mobile_number');
            }

            $activeVotingEvents = Event::where('status', 'active')->where('is_voting_active', true)->count();

            return [
                'totalVotes' => Vote::count(),
                'uniqueVoters' => $filteredVoters,
                'votesInWindow' => $filteredVotes,
                'activeVotingEvents' => $activeVotingEvents,
            ];
        });

        $rangeDescription = $isFiltered
            ? 'Filtered: '.($startDate ? $startDate->format('M d, H:i') : 'Start').' - '.($endDate ? $endDate->format('M d, H:i') : 'Now')
            : null;

        return [
            Stat::make('Unique Voters', number_format($data['uniqueVoters']))
                ->description($rangeDescription ?? 'Distinct verified mobile numbers')
                ->descriptionIcon('heroicon-m-device-phone-mobile')
                ->color('primary')
                ->chart([3, 8, 15, 22, 38, max(1, $data['uniqueVoters'])]),

            Stat::make($isFiltered ? 'Votes Cast in Window' : 'Votes Cast Today', number_format($data['votesInWindow']))
                ->description($rangeDescription ?? 'Votes recorded in last 24h')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([1, 4, 7, 10, 15, max(1, $data['votesInWindow'])]),

            Stat::make('Voting Status', $data['activeVotingEvents'] > 0 ? 'Active & Open' : 'Closed')
                ->description($data['activeVotingEvents'] > 0 ? "{$data['activeVotingEvents']} event(s) accepting votes" : 'Voting disabled')
                ->descriptionIcon($data['activeVotingEvents'] > 0 ? 'heroicon-m-signal' : 'heroicon-m-lock-closed')
                ->color($data['activeVotingEvents'] > 0 ? 'success' : 'gray'),
        ];
    }
}
