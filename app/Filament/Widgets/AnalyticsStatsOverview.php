<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use App\Models\UserActivity;
use App\Models\Vote;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnalyticsStatsOverview extends StatsOverviewWidget
{
    public ?int $eventId = null;

    public string $period = '7days';

    protected static bool $isLazy = false;

    /**
     * Resolve date bounds for the current active filter period.
     *
     * @return array{0: Carbon, 1: Carbon, 2: Carbon, 3: Carbon}
     */
    protected function getDateRanges(): array
    {
        $now = now();

        return match ($this->period) {
            'today' => [
                today()->startOfDay(),
                $now,
                today()->subDay()->startOfDay(),
                today()->subDay()->endOfDay(),
            ],
            'yesterday' => [
                today()->subDay()->startOfDay(),
                today()->subDay()->endOfDay(),
                today()->subDays(2)->startOfDay(),
                today()->subDays(2)->endOfDay(),
            ],
            '30days' => [
                now()->subDays(30)->startOfDay(),
                $now,
                now()->subDays(60)->startOfDay(),
                now()->subDays(30)->startOfDay(),
            ],
            'all' => [
                now()->subYears(5)->startOfDay(),
                $now,
                now()->subYears(10)->startOfDay(),
                now()->subYears(5)->startOfDay(),
            ],
            default => [ // 7days
                now()->subDays(7)->startOfDay(),
                $now,
                now()->subDays(14)->startOfDay(),
                now()->subDays(7)->startOfDay(),
            ],
        };
    }

    protected function getStats(): array
    {
        [$startDate, $endDate, $prevStartDate, $prevEndDate] = $this->getDateRanges();

        // 1. Page Views
        $viewsQuery = PageView::query()->forEvent($this->eventId);
        $totalViews = (clone $viewsQuery)->whereBetween('created_at', [$startDate, $endDate])->count();
        $prevViews = (clone $viewsQuery)->whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        $viewsTrend = $this->calculatePercentageChange($totalViews, $prevViews);

        // 2. Unique Visitors
        $visitors = (clone $viewsQuery)->whereBetween('created_at', [$startDate, $endDate])
            ->distinct('visitor_id')
            ->count('visitor_id');
        $prevVisitors = (clone $viewsQuery)->whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->distinct('visitor_id')
            ->count('visitor_id');
        $visitorsTrend = $this->calculatePercentageChange($visitors, $prevVisitors);

        // 3. Pandal Searches
        $activityQuery = UserActivity::query()->forEvent($this->eventId);
        $searches = (clone $activityQuery)->action('search')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // 4. Map "Get Directions" Navigation Clicks
        $mapClicks = (clone $activityQuery)->action('map_directions_click')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // 5. Voting Funnel Conversion
        $voteScreenViews = (clone $viewsQuery)->where('page_type', 'vote_screen')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $completedVotes = Vote::query()->when($this->eventId, fn ($q) => $q->where('event_id', $this->eventId))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $conversionRate = $voteScreenViews > 0
            ? round(($completedVotes / $voteScreenViews) * 100, 1)
            : 0;

        return [
            Stat::make('Total Page Views', number_format($totalViews))
                ->description($viewsTrend['text'].' vs prev period')
                ->descriptionIcon($viewsTrend['icon'])
                ->color($viewsTrend['color'])
                ->chart($this->getDailyTrendData(PageView::class, $startDate, $endDate)),

            Stat::make('Unique Visitors', number_format($visitors))
                ->description($visitorsTrend['text'].' distinct audience')
                ->descriptionIcon($visitorsTrend['icon'])
                ->color($visitorsTrend['color'])
                ->chart([3, 8, 14, 22, 35, max(1, $visitors)]),

            Stat::make('Pandal Searches', number_format($searches))
                ->description('Keywords searched in directory')
                ->descriptionIcon('heroicon-m-magnifying-glass')
                ->color('info')
                ->chart([2, 5, 7, 10, 16, max(1, $searches)]),

            Stat::make('Map Navigations', number_format($mapClicks))
                ->description('"Get Directions" pandal clicks')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('warning')
                ->chart([1, 4, 8, 12, 19, max(1, $mapClicks)]),

            Stat::make('Vote Conversion', "{$conversionRate}%")
                ->description("{$completedVotes} votes / {$voteScreenViews} vote screen visits")
                ->descriptionIcon('heroicon-m-arrow-path-rounded-square')
                ->color($conversionRate >= 20 ? 'success' : 'gray')
                ->chart([5, 12, 20, 28, max(5, (int) $conversionRate)]),
        ];
    }

    /**
     * Compute percentage change and icon indicator.
     *
     * @return array{text: string, icon: string, color: string}
     */
    protected function calculatePercentageChange(int $current, int $previous): array
    {
        if ($previous === 0) {
            return [
                'text' => $current > 0 ? '+100%' : '0%',
                'icon' => $current > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-minus',
                'color' => $current > 0 ? 'success' : 'gray',
            ];
        }

        $change = round((($current - $previous) / $previous) * 100);

        if ($change > 0) {
            return [
                'text' => "+{$change}%",
                'icon' => 'heroicon-m-arrow-trending-up',
                'color' => 'success',
            ];
        } elseif ($change < 0) {
            return [
                'text' => "{$change}%",
                'icon' => 'heroicon-m-arrow-trending-down',
                'color' => 'danger',
            ];
        }

        return [
            'text' => '0%',
            'icon' => 'heroicon-m-minus',
            'color' => 'gray',
        ];
    }

    /**
     * Retrieve aggregated counts across days for miniature sparklines.
     */
    protected function getDailyTrendData(string $modelClass, Carbon $startDate, Carbon $endDate): array
    {
        $daysCount = max(2, min(7, (int) $startDate->diffInDays($endDate) + 1));
        $data = [];

        for ($i = $daysCount - 1; $i >= 0; $i--) {
            $day = (clone $endDate)->subDays($i);
            $count = $modelClass::query()
                ->when($this->eventId, fn ($q) => $q->where('event_id', $this->eventId))
                ->whereDate('created_at', $day)
                ->count();
            $data[] = $count;
        }

        return ! empty($data) ? $data : [0, 0, 0];
    }
}
