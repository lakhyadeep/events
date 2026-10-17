<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\Analytics;
use App\Models\PageView;
use App\Models\UserActivity;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnalyticsQuickStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $data = cache()->remember('filament_analytics_quick_stats_data', now()->addSeconds(30), function (): array {
            $todayStart = today()->startOfDay();
            $viewsToday = PageView::where('created_at', '>=', $todayStart)->count();
            $visitorsToday = PageView::where('created_at', '>=', $todayStart)->distinct('visitor_id')->count('visitor_id');
            $votesToday = UserActivity::where('action_type', 'vote_verified')->where('created_at', '>=', $todayStart)->count();
            $searchesToday = UserActivity::where('action_type', 'search')->where('created_at', '>=', $todayStart)->count();

            return [
                'viewsToday' => $viewsToday,
                'visitorsToday' => $visitorsToday,
                'votesToday' => $votesToday,
                'searchesToday' => $searchesToday,
            ];
        });

        $analyticsUrl = Analytics::getUrl();

        return [
            Stat::make('Today\'s Page Views', number_format($data['viewsToday']))
                ->description('Public traffic across microsites')
                ->descriptionIcon('heroicon-m-eye')
                ->color('amber')
                ->url($analyticsUrl)
                ->chart([5, 12, 18, 25, 34, max(1, $data['viewsToday'])]),

            Stat::make('Today\'s Unique Visitors', number_format($data['visitorsToday']))
                ->description('Distinct audience devices')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->url($analyticsUrl)
                ->chart([3, 8, 14, 20, 28, max(1, $data['visitorsToday'])]),

            Stat::make('Today\'s Verified Votes', number_format($data['votesToday']))
                ->description("{$data['votesToday']} votes verified today")
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->url($analyticsUrl)
                ->chart([1, 4, 7, 12, 18, max(1, $data['votesToday'])]),
        ];
    }
}
