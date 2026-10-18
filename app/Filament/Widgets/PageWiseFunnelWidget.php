<?php

namespace App\Filament\Widgets;

use App\Models\Otp;
use App\Models\PageView;
use App\Models\UserActivity;
use App\Models\Vote;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class PageWiseFunnelWidget extends TableWidget
{
    public ?int $eventId = null;

    public string $period = '7days';

    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Page-Wise Tracking & Voting Conversion Funnel')
            ->description(function (): string {
                $summary = $this->getSummaryMetrics();

                return "Stage-to-stage transition rates & drop-off analysis • {$summary['overallConversion']}% overall conversion ({$summary['totalVotes']} verified votes) • {$summary['periodLabel']}";
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

        $periodLabels = [
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            '7days' => 'Last 7 Days',
            '30days' => 'Last 30 Days',
            'all' => 'All Time',
        ];

        return [
            'overallConversion' => $homeVisitors > 0 ? round(($voteVerified / $homeVisitors) * 100, 1) : 0,
            'totalVotes' => number_format($voteVerified),
            'periodLabel' => $periodLabels[$this->period] ?? 'Selected Period',
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
