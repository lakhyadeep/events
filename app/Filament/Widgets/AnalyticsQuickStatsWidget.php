<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\Analytics;
use App\Models\PageView;
use App\Models\UserActivity;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnalyticsQuickStatsWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 0;

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

        $cacheKey = 'filament_analytics_quick_stats_'.($isFiltered ? md5(json_encode($this->pageFilters)) : 'today');

        $data = cache()->remember($cacheKey, now()->addSeconds(15), function () use ($startDate, $endDate, $isFiltered): array {
            $viewsQuery = PageView::query();
            $visitorsQuery = PageView::query();
            $votesQuery = UserActivity::where('action_type', 'vote_verified');
            $searchesQuery = UserActivity::where('action_type', 'search');

            if ($isFiltered) {
                if ($startDate) {
                    $viewsQuery->where('created_at', '>=', $startDate);
                    $visitorsQuery->where('created_at', '>=', $startDate);
                    $votesQuery->where('created_at', '>=', $startDate);
                    $searchesQuery->where('created_at', '>=', $startDate);
                }
                if ($endDate) {
                    $viewsQuery->where('created_at', '<=', $endDate);
                    $visitorsQuery->where('created_at', '<=', $endDate);
                    $votesQuery->where('created_at', '<=', $endDate);
                    $searchesQuery->where('created_at', '<=', $endDate);
                }
            } else {
                $todayStart = today()->startOfDay();
                $viewsQuery->where('created_at', '>=', $todayStart);
                $visitorsQuery->where('created_at', '>=', $todayStart);
                $votesQuery->where('created_at', '>=', $todayStart);
                $searchesQuery->where('created_at', '>=', $todayStart);
            }

            return [
                'viewsToday' => $viewsQuery->count(),
                'visitorsToday' => $visitorsQuery->distinct('visitor_id')->count('visitor_id'),
                'votesToday' => $votesQuery->count(),
                'searchesToday' => $searchesQuery->count(),
            ];
        });

        $analyticsUrl = Analytics::getUrl();

        $rangeDescription = $isFiltered
            ? 'Filtered: '.($startDate ? $startDate->format('M d, H:i') : 'Start').' - '.($endDate ? $endDate->format('M d, H:i') : 'Now')
            : null;

        return [
            Stat::make($isFiltered ? 'Page Views (Filtered)' : 'Today\'s Page Views', number_format($data['viewsToday']))
                ->description($rangeDescription ?? 'Public traffic across microsites')
                ->descriptionIcon('heroicon-m-eye')
                ->color('amber')
                ->url($analyticsUrl)
                ->chart([5, 12, 18, 25, 34, max(1, $data['viewsToday'])]),

            Stat::make($isFiltered ? 'Unique Visitors (Filtered)' : 'Today\'s Unique Visitors', number_format($data['visitorsToday']))
                ->description($rangeDescription ?? 'Distinct audience devices')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->url($analyticsUrl)
                ->chart([3, 8, 14, 20, 28, max(1, $data['visitorsToday'])]),

            Stat::make($isFiltered ? 'Verified Votes (Filtered)' : 'Today\'s Verified Votes', number_format($data['votesToday']))
                ->description($rangeDescription ?? "{$data['votesToday']} votes verified today")
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->url($analyticsUrl)
                ->chart([1, 4, 7, 12, 18, max(1, $data['votesToday'])]),
        ];
    }
}
