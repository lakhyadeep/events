<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use App\Models\UserActivity;
use App\Models\Vote;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class PageWiseFunnelWidget extends Widget
{
    public ?int $eventId = null;

    public string $period = '7days';

    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.page-wise-funnel';

    protected function getViewData(): array
    {
        return [
            'funnelData' => $this->getFunnelData(),
        ];
    }

    /**
     * Resolve date bounds for the current active filter period.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function getDateRanges(): array
    {
        $now = now();

        return match ($this->period) {
            'today' => [today()->startOfDay(), $now],
            'yesterday' => [today()->subDay()->startOfDay(), today()->subDay()->endOfDay()],
            '30days' => [now()->subDays(30)->startOfDay(), $now],
            'all' => [now()->subYears(5)->startOfDay(), $now],
            default => [now()->subDays(7)->startOfDay(), $now],
        };
    }

    /**
     * Compile page-wise navigation and voting funnel metrics.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getFunnelData(): array
    {
        [$startDate, $endDate] = $this->getDateRanges();

        $viewsQuery = PageView::query()->forEvent($this->eventId)->whereBetween('created_at', [$startDate, $endDate]);
        $activityQuery = UserActivity::query()->forEvent($this->eventId)->whereBetween('created_at', [$startDate, $endDate]);

        // 1. Homepage
        $homeViews = (clone $viewsQuery)->whereIn('page_type', ['homepage', 'home', 'landing'])->count();
        $homeVisitors = (clone $viewsQuery)->whereIn('page_type', ['homepage', 'home', 'landing'])->distinct('visitor_id')->count('visitor_id');

        // 2. All Pujas Directory
        $allPujasViews = (clone $viewsQuery)->whereIn('page_type', ['all_puja', 'all_pujas'])->count();
        $allPujasVisitors = (clone $viewsQuery)->whereIn('page_type', ['all_puja', 'all_pujas'])->distinct('visitor_id')->count('visitor_id');

        // 3. Vote Screen
        $voteViews = (clone $viewsQuery)->whereIn('page_type', ['vote', 'vote_screen'])->count();
        $voteVisitors = (clone $viewsQuery)->whereIn('page_type', ['vote', 'vote_screen'])->distinct('visitor_id')->count('visitor_id');

        // 4. Vote Initiated (OTP Requested / Form Submitted)
        $voteInitiated = (clone $activityQuery)->action('vote_initiated')->count();
        $voteInitiatedVisitors = (clone $activityQuery)->action('vote_initiated')->distinct('visitor_id')->count('visitor_id');

        // 5. Vote Verified (OTP Verified & Cast)
        $voteVerifiedAct = (clone $activityQuery)->action('vote_verified')->count();
        $officialVotes = Vote::query()->when($this->eventId, fn ($q) => $q->where('event_id', $this->eventId))
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        $voteVerified = max($voteVerifiedAct, $officialVotes);
        $voteVerifiedVisitors = (clone $activityQuery)->action('vote_verified')->distinct('visitor_id')->count('visitor_id');
        if ($voteVerifiedVisitors === 0 && $officialVotes > 0) {
            $voteVerifiedVisitors = Vote::query()->when($this->eventId, fn ($q) => $q->where('event_id', $this->eventId))
                ->whereBetween('created_at', [$startDate, $endDate])->distinct('mobile_number')->count('mobile_number');
        }

        // 6. Vote Success Screen
        $voteSuccessViews = (clone $viewsQuery)->where('page_type', 'vote_success')->count();
        $voteSuccessVisitors = (clone $viewsQuery)->where('page_type', 'vote_success')->distinct('visitor_id')->count('visitor_id');

        return [
            [
                'step' => 1,
                'name' => 'Homepage',
                'description' => 'Festive awards landing page & introduction',
                'type' => 'Page View',
                'views' => $homeViews,
                'visitors' => $homeVisitors,
                'conversion_label' => 'Entry Baseline',
                'pct' => 100,
                'icon' => 'heroicon-m-home',
                'badge_color' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
            ],
            [
                'step' => 2,
                'name' => 'All Puja Directory',
                'description' => 'Directory of participating pandals & zone search',
                'type' => 'Page View',
                'views' => $allPujasViews,
                'visitors' => $allPujasVisitors,
                'conversion_label' => $homeViews > 0 ? round(($allPujasViews / $homeViews) * 100, 1).'% from Home' : '0%',
                'pct' => $homeViews > 0 ? min(100, round(($allPujasViews / $homeViews) * 100)) : 0,
                'icon' => 'heroicon-m-rectangle-stack',
                'badge_color' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
            ],
            [
                'step' => 3,
                'name' => 'Vote Screen',
                'description' => 'Participant voting form with categories',
                'type' => 'Page View',
                'views' => $voteViews,
                'visitors' => $voteVisitors,
                'conversion_label' => $allPujasViews > 0 ? round(($voteViews / $allPujasViews) * 100, 1).'% from Directory' : ($homeViews > 0 ? round(($voteViews / $homeViews) * 100, 1).'% from Home' : '0%'),
                'pct' => $allPujasViews > 0 ? min(100, round(($voteViews / $allPujasViews) * 100)) : 0,
                'icon' => 'heroicon-m-cursor-arrow-rays',
                'badge_color' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
            ],
            [
                'step' => 4,
                'name' => 'Vote Initiated',
                'description' => 'Mobile number entered & SMS OTP requested',
                'type' => 'User Action',
                'views' => $voteInitiated,
                'visitors' => $voteInitiatedVisitors,
                'conversion_label' => $voteViews > 0 ? round(($voteInitiated / $voteViews) * 100, 1).'% of Vote Views' : '0%',
                'pct' => $voteViews > 0 ? min(100, round(($voteInitiated / $voteViews) * 100)) : 0,
                'icon' => 'heroicon-m-device-phone-mobile',
                'badge_color' => 'bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
            ],
            [
                'step' => 5,
                'name' => 'Vote Verified',
                'description' => 'OTP verified & vote committed to ledger',
                'type' => 'User Action',
                'views' => $voteVerified,
                'visitors' => $voteVerifiedVisitors,
                'conversion_label' => $voteInitiated > 0 ? round(($voteVerified / $voteInitiated) * 100, 1).'% of Initiated' : '0%',
                'pct' => $voteInitiated > 0 ? min(100, round(($voteVerified / $voteInitiated) * 100)) : 0,
                'icon' => 'heroicon-m-check-badge',
                'badge_color' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
            ],
            [
                'step' => 6,
                'name' => 'Vote Success',
                'description' => 'Confirmation screen with social share badge',
                'type' => 'Page View',
                'views' => $voteSuccessViews,
                'visitors' => $voteSuccessVisitors,
                'conversion_label' => $voteVerified > 0 ? round(($voteSuccessViews / $voteVerified) * 100, 1).'% of Verified' : '0%',
                'pct' => $voteVerified > 0 ? min(100, round(($voteSuccessViews / $voteVerified) * 100)) : 0,
                'icon' => 'heroicon-m-sparkles',
                'badge_color' => 'bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
            ],
        ];
    }
}
