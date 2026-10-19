<?php

namespace App\Filament\Widgets;

use App\Models\Otp;
use App\Models\PageView;
use App\Models\UserActivity;
use App\Models\Vote;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Livewire\Attributes\On;

class PageWiseFunnelWidget extends TableWidget
{
    use InteractsWithPageFilters;

    public ?int $eventId = null;

    public string $period = 'last_7_days';

    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    #[On('analytics-filter-updated')]
    public function updateFilters(?int $eventId, string $period): void
    {
        $this->eventId = $eventId ?: null;
        $this->period = $period;
        $this->resetTable();
    }

    public function updatedPageFilters(): void
    {
        $this->resetTable();
    }

    protected function getActiveEventId(): ?int
    {
        $id = $this->pageFilters['eventId'] ?? $this->eventId ?? null;

        return $id ? (int) $id : null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Page-Wise Tracking & Voting Conversion Funnel')
            ->description(function (): string {
                $summary = $this->getSummaryMetrics();
                $eventId = $this->getActiveEventId();
                $scope = $eventId ? 'Event #'.$eventId : 'Global Scope';

                return "Stage-to-stage transition rates & drop-off analysis • {$summary['overallConversion']}% overall conversion ({$summary['totalVotes']} verified votes) • {$summary['periodLabel']} • {$scope}";
            })
            ->records(fn (): array => $this->getFunnelData())
            ->paginated(false)
            ->columns([
                TextColumn::make('step')
                    ->label('#')
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('name')
                    ->label('Journey Stage / Page')
                    ->description(fn (array $record): string => $record['description'])
                    ->icon(fn (array $record): string => $record['icon'])
                    ->weight('bold'),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Page View' => 'info',
                        'User Action' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Page View' => 'heroicon-m-globe-alt',
                        'User Action' => 'heroicon-m-bolt',
                        default => 'heroicon-m-sparkles',
                    }),

                TextColumn::make('views')
                    ->label('Total Hits')
                    ->numeric()
                    ->alignEnd()
                    ->weight('bold'),

                TextColumn::make('visitors')
                    ->label('Unique Visitors')
                    ->numeric()
                    ->alignEnd()
                    ->icon('heroicon-o-users'),

                TextColumn::make('step_conversion')
                    ->label('Step Conversion')
                    ->state(fn (array $record): string => $record['step'] === 1
                        ? '100% Entry Baseline'
                        : "{$record['step_pct']}% proceeded"
                    )
                    ->description(fn (array $record): ?string => ($record['step'] > 1 && $record['dropoff_count'] > 0)
                        ? '-'.number_format($record['dropoff_count'])." drop ({$record['dropoff_pct']}%)"
                        : null
                    )
                    ->badge()
                    ->color(fn (array $record): string => $record['step'] === 1
                        ? 'gray'
                        : ($record['step_pct'] >= 75 ? 'success' : ($record['step_pct'] >= 40 ? 'warning' : 'danger'))
                    )
                    ->icon(fn (array $record): ?string => $record['step'] === 1 ? null : 'heroicon-m-arrow-down-right')
                    ->alignEnd(),

                TextColumn::make('baseline_pct')
                    ->label('Funnel Retention')
                    ->formatStateUsing(fn ($state): string => "{$state}%")
                    ->badge()
                    ->color(fn ($state): string => $state >= 50 ? 'success' : ($state >= 20 ? 'warning' : 'gray'))
                    ->alignEnd(),
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getSummaryMetrics(): array
    {
        $funnelData = $this->getFunnelData();

        $homeVisitors = $funnelData[0]['visitors'] ?? 0;
        $voteVerified = $funnelData[4]['views'] ?? 0;

        $preset = $this->pageFilters['rangePreset'] ?? $this->period ?? 'last_7_days';

        $periodLabels = [
            'last_30_mins' => 'Last 30 Minutes',
            'last_hour' => 'Last 1 Hour',
            'last_4_hours' => 'Last 4 Hours',
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            'last_24_hours' => 'Last 24 Hours',
            'last_7_days' => 'Last 7 Days',
            'last_30_days' => 'Last 30 Days',
            'this_month' => 'This Month',
            'all_time' => 'All Time',
            // Aliases
            '30m' => 'Last 30 Minutes',
            '1h' => 'Last 1 Hour',
            '4h' => 'Last 4 Hours',
            '24h' => 'Last 24 Hours',
            '7days' => 'Last 7 Days',
            '30days' => 'Last 30 Days',
            'all' => 'All Time',
        ];

        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;
        if (! empty($startDate) && ! empty($endDate)) {
            $label = Carbon::parse($startDate)->format('M d, H:i').' - '.Carbon::parse($endDate)->format('M d, H:i');
        } elseif (! empty($startDate)) {
            $label = 'From '.Carbon::parse($startDate)->format('M d, H:i');
        } else {
            $label = $periodLabels[$preset] ?? 'Selected Period';
        }

        return [
            'overallConversion' => $homeVisitors > 0 ? round(($voteVerified / $homeVisitors) * 100, 1) : 0,
            'totalVotes' => number_format($voteVerified),
            'periodLabel' => $label,
        ];
    }

    /**
     * Resolve date bounds for the current active filter period.
     *
     * @return array{0: Carbon, 1: Carbon}
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
            return [
                $startDate ?? now()->subDays(7)->startOfDay(),
                $endDate ?? now(),
            ];
        }

        $now = now();
        $preset = $this->pageFilters['rangePreset'] ?? $this->period ?? 'last_7_days';

        return match ($preset) {
            'last_30_mins', '30m', '30mins' => [$now->clone()->subMinutes(30), $now],
            'last_hour', '1h', '1hour' => [$now->clone()->subHour(), $now],
            'last_4_hours', '4h', '4hours' => [$now->clone()->subHours(4), $now],
            'today' => [today()->startOfDay(), $now],
            'yesterday' => [today()->subDay()->startOfDay(), today()->subDay()->endOfDay()],
            'last_24_hours', '24h', '24hours' => [$now->clone()->subHours(24), $now],
            'last_7_days', '7days' => [now()->subDays(7)->startOfDay(), $now],
            'last_30_days', '30days' => [now()->subDays(30)->startOfDay(), $now],
            'this_month' => [now()->startOfMonth(), $now],
            'all_time', 'all' => [now()->subYears(5)->startOfDay(), $now],
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
        $eventId = $this->getActiveEventId();

        $viewsQuery = PageView::query()->forEvent($eventId)->whereBetween('created_at', [$startDate, $endDate]);
        $activityQuery = UserActivity::query()->forEvent($eventId)->whereBetween('created_at', [$startDate, $endDate]);

        // 1. Homepage
        $homeViews = (clone $viewsQuery)->whereIn('page_type', ['homepage', 'home', 'landing'])->count();
        $homeVisitors = (clone $viewsQuery)->whereIn('page_type', ['homepage', 'home', 'landing'])->distinct('visitor_id')->count('visitor_id');

        // 2. All Pujas Directory
        $allPujasViews = (clone $viewsQuery)->whereIn('page_type', ['all_puja', 'all_pujas'])->count();
        $allPujasVisitors = (clone $viewsQuery)->whereIn('page_type', ['all_puja', 'all_pujas'])->distinct('visitor_id')->count('visitor_id');

        // 3. Vote Screen
        $voteViews = (clone $viewsQuery)->whereIn('page_type', ['vote', 'vote_screen'])->count();
        $voteVisitors = (clone $viewsQuery)->whereIn('page_type', ['vote', 'vote_screen'])->distinct('visitor_id')->count('visitor_id');

        // 5. Vote Verified (OTP Verified & Cast)
        $voteVerifiedAct = (clone $activityQuery)->action('vote_verified')->count();
        $officialVotes = Vote::query()->when($eventId, fn ($q) => $q->where('event_id', $eventId))
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        $voteVerified = max($voteVerifiedAct, $officialVotes);
        $voteVerifiedVisitors = (clone $activityQuery)->action('vote_verified')->distinct('visitor_id')->count('visitor_id');
        if ($voteVerifiedVisitors === 0 && $officialVotes > 0) {
            $voteVerifiedVisitors = Vote::query()->when($eventId, fn ($q) => $q->where('event_id', $eventId))
                ->whereBetween('created_at', [$startDate, $endDate])->distinct('mobile_number')->count('mobile_number');
        }

        // 4. Vote Initiated (OTP Requested / Form Submitted)
        $voteInitiatedAct = (clone $activityQuery)->action('vote_initiated')->count();
        $officialOtps = Otp::query()->where('purpose', 'voting')
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        // A vote cannot be verified without having been initiated:
        $voteInitiated = max($voteInitiatedAct, $officialOtps, $voteVerified);

        $voteInitiatedVisitors = (clone $activityQuery)->action('vote_initiated')->distinct('visitor_id')->count('visitor_id');
        $officialOtpPhones = Otp::query()->where('purpose', 'voting')
            ->whereBetween('created_at', [$startDate, $endDate])->distinct('phone')->count('phone');
        $voteInitiatedVisitors = max($voteInitiatedVisitors, $officialOtpPhones, $voteVerifiedVisitors);

        // 6. Vote Success Screen
        $voteSuccessViews = (clone $viewsQuery)->where('page_type', 'vote_success')->count();
        $voteSuccessVisitors = (clone $viewsQuery)->where('page_type', 'vote_success')->distinct('visitor_id')->count('visitor_id');

        $steps = [
            [
                'key' => '1',
                'step' => 1,
                'name' => 'Homepage',
                'description' => 'Festive awards landing page & introduction',
                'type' => 'Page View',
                'views' => $homeViews,
                'visitors' => $homeVisitors,
                'icon' => 'heroicon-m-home',
            ],
            [
                'key' => '2',
                'step' => 2,
                'name' => 'All Puja Directory',
                'description' => 'Directory of participating pandals & zone search',
                'type' => 'Page View',
                'views' => $allPujasViews,
                'visitors' => $allPujasVisitors,
                'icon' => 'heroicon-m-rectangle-stack',
            ],
            [
                'key' => '3',
                'step' => 3,
                'name' => 'Vote Screen',
                'description' => 'Participant voting form with categories',
                'type' => 'Page View',
                'views' => $voteViews,
                'visitors' => $voteVisitors,
                'icon' => 'heroicon-m-cursor-arrow-rays',
            ],
            [
                'key' => '4',
                'step' => 4,
                'name' => 'Vote Initiated',
                'description' => 'Mobile number entered & SMS OTP requested',
                'type' => 'User Action',
                'views' => $voteInitiated,
                'visitors' => $voteInitiatedVisitors,
                'icon' => 'heroicon-m-device-phone-mobile',
            ],
            [
                'key' => '5',
                'step' => 5,
                'name' => 'Vote Verified',
                'description' => 'OTP verified & vote committed to ledger',
                'type' => 'User Action',
                'views' => $voteVerified,
                'visitors' => $voteVerifiedVisitors,
                'icon' => 'heroicon-m-check-badge',
            ],
            [
                'key' => '6',
                'step' => 6,
                'name' => 'Vote Success',
                'description' => 'Confirmation screen with social share badge',
                'type' => 'Page View',
                'views' => $voteSuccessViews,
                'visitors' => $voteSuccessVisitors,
                'icon' => 'heroicon-m-sparkles',
            ],
        ];

        $baselineViews = max(1, $homeViews);

        foreach ($steps as $i => &$step) {
            $step['baseline_pct'] = round(($step['views'] / $baselineViews) * 100, 1);

            if ($i === 0) {
                $step['step_pct'] = 100;
                $step['dropoff_count'] = 0;
                $step['dropoff_pct'] = 0;
            } else {
                $prevStepViews = $steps[$i - 1]['views'];
                $step['step_pct'] = $prevStepViews > 0 ? round(($step['views'] / $prevStepViews) * 100, 1) : 0;
                $step['dropoff_count'] = max(0, $prevStepViews - $step['views']);
                $step['dropoff_pct'] = $prevStepViews > 0 ? round(($step['dropoff_count'] / $prevStepViews) * 100, 1) : 0;
            }
        }

        return $steps;
    }
}
