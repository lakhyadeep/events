<?php

namespace App\Filament\Widgets;

use App\Models\Otp;
use App\Models\PageView;
use App\Models\UserActivity;
use App\Models\Vote;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\On;

class AnalyticsStatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    public ?int $eventId = null;

    public string $period = 'last_7_days';

    protected static bool $isLazy = false;

    #[On('analytics-filter-updated')]
    public function updateFilters(?int $eventId, string $period): void
    {
        $this->eventId = $eventId ?: null;
        $this->period = $period;
    }

    protected function getActiveEventId(): ?int
    {
        $id = $this->pageFilters['eventId'] ?? $this->eventId ?? null;

        return $id ? (int) $id : null;
    }

    /**
     * Resolve date bounds for current and previous periods.
     *
     * @return array{0: Carbon, 1: Carbon, 2: Carbon, 3: Carbon}
     */
    protected function getDateRanges(): array
    {
        $startDate = ! empty($this->pageFilters['startDate'] ?? null)
            ? rescue(fn () => Carbon::parse($this->pageFilters['startDate']), null, false)
            : null;

        $endDate = ! empty($this->pageFilters['endDate'] ?? null)
            ? rescue(fn () => Carbon::parse($this->pageFilters['endDate']), null, false)
            : null;

        if ($startDate || $endDate) {
            $start = $startDate ?? now()->subDays(7)->startOfDay();
            $end = $endDate ?? now();
            $diffSeconds = max(1, (int) $end->diffInSeconds($start));
            $prevEnd = $start->clone()->subSecond();
            $prevStart = $prevEnd->clone()->subSeconds($diffSeconds);

            return [$start, $end, $prevStart, $prevEnd];
        }

        $now = now();
        $preset = $this->pageFilters['rangePreset'] ?? $this->period ?? 'last_7_days';

        return match ($preset) {
            'last_30_mins', '30m', '30mins' => [
                $now->clone()->subMinutes(30),
                $now,
                $now->clone()->subMinutes(60),
                $now->clone()->subMinutes(30),
            ],
            'last_hour', '1h', '1hour' => [
                $now->clone()->subHour(),
                $now,
                $now->clone()->subHours(2),
                $now->clone()->subHour(),
            ],
            'last_4_hours', '4h', '4hours' => [
                $now->clone()->subHours(4),
                $now,
                $now->clone()->subHours(8),
                $now->clone()->subHours(4),
            ],
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
            'last_24_hours', '24h', '24hours' => [
                $now->clone()->subHours(24),
                $now,
                $now->clone()->subHours(48),
                $now->clone()->subHours(24),
            ],
            'last_7_days', '7days' => [
                now()->subDays(7)->startOfDay(),
                $now,
                now()->subDays(14)->startOfDay(),
                now()->subDays(7)->startOfDay(),
            ],
            'last_30_days', '30days' => [
                now()->subDays(30)->startOfDay(),
                $now,
                now()->subDays(60)->startOfDay(),
                now()->subDays(30)->startOfDay(),
            ],
            'this_month' => [
                now()->startOfMonth(),
                $now,
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth(),
            ],
            'all_time', 'all' => [
                now()->subYears(5)->startOfDay(),
                $now,
                now()->subYears(10)->startOfDay(),
                now()->subYears(5)->startOfDay(),
            ],
            default => [
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
        $eventId = $this->getActiveEventId();

        $viewsQuery = PageView::query()->forEvent($eventId);
        $activityQuery = UserActivity::query()->forEvent($eventId);

        // 1. Homepage Views
        $homepageViews = (clone $viewsQuery)->whereIn('page_type', ['homepage', 'home', 'landing'])
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        $prevHomepageViews = (clone $viewsQuery)->whereIn('page_type', ['homepage', 'home', 'landing'])
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        $homepageTrend = $this->calculatePercentageChange($homepageViews, $prevHomepageViews);

        // 2. All Pujas Directory Views
        $allPujasViews = (clone $viewsQuery)->whereIn('page_type', ['all_puja', 'all_pujas'])
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        $prevAllPujasViews = (clone $viewsQuery)->whereIn('page_type', ['all_puja', 'all_pujas'])
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        $allPujasTrend = $this->calculatePercentageChange($allPujasViews, $prevAllPujasViews);

        // 3. Vote Screen Views
        $voteScreenViews = (clone $viewsQuery)->whereIn('page_type', ['vote', 'vote_screen'])
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        $prevVoteScreenViews = (clone $viewsQuery)->whereIn('page_type', ['vote', 'vote_screen'])
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        $voteScreenTrend = $this->calculatePercentageChange($voteScreenViews, $prevVoteScreenViews);

        // 5. Votes Verified (OTP Verified & Vote Cast Atomically)
        $voteVerified = (clone $activityQuery)->action('vote_verified')
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        $officialVotes = Vote::query()->when($eventId, fn ($q) => $q->where('event_id', $eventId))
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        $effectiveVerified = max($voteVerified, $officialVotes);

        $prevVoteVerified = (clone $activityQuery)->action('vote_verified')
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        $prevOfficialVotes = Vote::query()->when($eventId, fn ($q) => $q->where('event_id', $eventId))
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        $prevEffectiveVerified = max($prevVoteVerified, $prevOfficialVotes);
        $voteVerifiedTrend = $this->calculatePercentageChange($effectiveVerified, $prevEffectiveVerified);

        // 4. Votes Initiated (OTP Requested / Vote Clicked)
        $voteInitiated = (clone $activityQuery)->action('vote_initiated')
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        $officialOtps = Otp::query()->where('purpose', 'voting')
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        $effectiveInitiated = max($voteInitiated, $officialOtps, $effectiveVerified);

        $prevVoteInitiated = (clone $activityQuery)->action('vote_initiated')
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        $prevOfficialOtps = Otp::query()->where('purpose', 'voting')
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        $prevEffectiveInitiated = max($prevVoteInitiated, $prevOfficialOtps, $prevEffectiveVerified);
        $voteInitiatedTrend = $this->calculatePercentageChange($effectiveInitiated, $prevEffectiveInitiated);

        // 6. Vote Success Screen Views
        $voteSuccessViews = (clone $viewsQuery)->where('page_type', 'vote_success')
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        $prevVoteSuccessViews = (clone $viewsQuery)->where('page_type', 'vote_success')
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        $voteSuccessTrend = $this->calculatePercentageChange($voteSuccessViews, $prevVoteSuccessViews);

        return [
            Stat::make('Homepage Views', number_format($homepageViews))
                ->description($homepageTrend['text'].' vs prev period')
                ->descriptionIcon($homepageTrend['icon'])
                ->color($homepageTrend['color'])
                ->chart([3, 8, 14, 22, 35, max(1, $homepageViews)]),

            Stat::make('All Puja Directory', number_format($allPujasViews))
                ->description($allPujasTrend['text'].' vs prev period')
                ->descriptionIcon($allPujasTrend['icon'])
                ->color($allPujasTrend['color'])
                ->chart([2, 6, 12, 18, 28, max(1, $allPujasViews)]),

            Stat::make('Vote Screen', number_format($voteScreenViews))
                ->description($voteScreenTrend['text'].' vs prev period')
                ->descriptionIcon($voteScreenTrend['icon'])
                ->color($voteScreenTrend['color'])
                ->chart([1, 4, 9, 15, 24, max(1, $voteScreenViews)]),

            Stat::make('Votes Initiated', number_format($effectiveInitiated))
                ->description($voteInitiatedTrend['text'].' OTPs requested')
                ->descriptionIcon('heroicon-m-device-phone-mobile')
                ->color('amber')
                ->chart([1, 3, 7, 12, 20, max(1, $effectiveInitiated)]),

            Stat::make('Votes Verified', number_format($effectiveVerified))
                ->description($voteVerifiedTrend['text'].' verified votes')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->chart([1, 2, 5, 10, 18, max(1, $effectiveVerified)]),

            Stat::make('Vote Success Views', number_format($voteSuccessViews))
                ->description($voteSuccessTrend['text'].' success page loads')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('primary')
                ->chart([1, 2, 5, 9, 16, max(1, $voteSuccessViews)]),
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

        $eventId = $this->getActiveEventId();
        for ($i = $daysCount - 1; $i >= 0; $i--) {
            $day = (clone $endDate)->subDays($i);
            $count = $modelClass::query()
                ->when($eventId, fn ($q) => $q->where('event_id', $eventId))
                ->whereDate('created_at', $day)
                ->count();
            $data[] = $count;
        }

        return ! empty($data) ? $data : [0, 0, 0];
    }
}
