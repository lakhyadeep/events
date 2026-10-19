<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Livewire\Attributes\On;

class AnalyticsTrafficChart extends ChartWidget
{
    use InteractsWithPageFilters;

    public ?int $eventId = null;

    public string $period = 'last_7_days';

    protected ?string $heading = 'Traffic Trends: Page Views & Unique Visitors';

    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 2,
    ];

    #[On('analytics-filter-updated')]
    public function updateFilters(?int $eventId, string $period): void
    {
        $this->eventId = $eventId ?: null;
        $this->period = $period;
        $this->updateChartData();
    }

    public function updatedPageFilters(): void
    {
        $this->updateChartData();
    }

    protected function getActiveEventId(): ?int
    {
        $id = $this->pageFilters['eventId'] ?? $this->eventId ?? null;

        return $id ? (int) $id : null;
    }

    /**
     * Resolve date bounds for the current active filter period.
     *
     * @return array{0: Carbon, 1: Carbon, 2: string}
     */
    protected function getDateRange(): array
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
            $diffHours = $end->diffInHours($start);
            $mode = $diffHours <= 1 ? 'minute_5' : ($diffHours <= 48 ? 'hour' : 'day');

            return [$start, $end, $mode];
        }

        $now = now();
        $preset = $this->pageFilters['rangePreset'] ?? $this->period ?? 'last_7_days';

        return match ($preset) {
            'last_30_mins', '30m', '30mins' => [$now->clone()->subMinutes(30), $now, 'minute_5'],
            'last_hour', '1h', '1hour' => [$now->clone()->subHour(), $now, 'minute_5'],
            'last_4_hours', '4h', '4hours' => [$now->clone()->subHours(4), $now, 'hour'],
            'today' => [today()->startOfDay(), $now, 'hour_today'],
            'yesterday' => [today()->subDay()->startOfDay(), today()->subDay()->endOfDay(), 'hour_yesterday'],
            'last_24_hours', '24h', '24hours' => [$now->clone()->subHours(24), $now, 'hour'],
            'last_7_days', '7days' => [now()->subDays(7)->startOfDay(), $now, 'day'],
            'last_30_days', '30days' => [now()->subDays(30)->startOfDay(), $now, 'day'],
            'this_month' => [now()->startOfMonth(), $now, 'day'],
            'all_time', 'all' => [now()->subYears(2)->startOfDay(), $now, 'day'],
            default => [now()->subDays(7)->startOfDay(), $now, 'day'],
        };
    }

    protected function getData(): array
    {
        [$startDate, $endDate, $mode] = $this->getDateRange();
        $eventId = $this->getActiveEventId();

        $labels = [];
        $viewsData = [];
        $visitorsData = [];

        if ($mode === 'minute' || $mode === 'minute_5' || $mode === 'minute_10') {
            $stepMinutes = $mode === 'minute' ? 1 : ($mode === 'minute_5' ? 5 : 10);
            $current = $startDate->clone()->second(0);

            while ($current <= $endDate) {
                $bucketStart = $current->clone();
                $bucketEnd = $current->clone()->addMinutes($stepMinutes)->subSecond();

                $labels[] = $bucketStart->format('H:i');

                $viewsData[] = PageView::query()
                    ->forEvent($eventId)
                    ->whereBetween('created_at', [$bucketStart, $bucketEnd])
                    ->count();

                $visitorsData[] = PageView::query()
                    ->forEvent($eventId)
                    ->whereBetween('created_at', [$bucketStart, $bucketEnd])
                    ->distinct('visitor_id')
                    ->count('visitor_id');

                $current->addMinutes($stepMinutes);
            }
        } elseif ($mode === 'hour' || $mode === 'hour_today' || $mode === 'hour_yesterday') {
            $current = $startDate->clone()->minute(0)->second(0);

            while ($current <= $endDate) {
                $bucketStart = $current->clone();
                $bucketEnd = $current->clone()->addHour()->subSecond();

                $labels[] = $bucketStart->format('H:i');

                $viewsData[] = PageView::query()
                    ->forEvent($eventId)
                    ->whereBetween('created_at', [$bucketStart, $bucketEnd])
                    ->count();

                $visitorsData[] = PageView::query()
                    ->forEvent($eventId)
                    ->whereBetween('created_at', [$bucketStart, $bucketEnd])
                    ->distinct('visitor_id')
                    ->count('visitor_id');

                $current->addHour();
            }
        } else {
            // Group by day
            $days = (int) $startDate->diffInDays($endDate);
            for ($i = $days; $i >= 0; $i--) {
                $day = (clone $endDate)->subDays($i)->startOfDay();
                $labels[] = $day->format('M d');

                $viewsData[] = PageView::query()
                    ->forEvent($eventId)
                    ->whereDate('created_at', $day)
                    ->count();

                $visitorsData[] = PageView::query()
                    ->forEvent($eventId)
                    ->whereDate('created_at', $day)
                    ->distinct('visitor_id')
                    ->count('visitor_id');
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Page Views',
                    'data' => $viewsData,
                    'borderColor' => '#f59e0b', // Amber-500
                    'backgroundColor' => 'rgba(245, 158, 11, 0.15)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Unique Visitors',
                    'data' => $visitorsData,
                    'borderColor' => '#0284c7', // Sky-600
                    'backgroundColor' => 'rgba(2, 132, 199, 0.1)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
