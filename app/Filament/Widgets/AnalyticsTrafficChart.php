<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\ChartWidget;

class AnalyticsTrafficChart extends ChartWidget
{
    public ?int $eventId = null;

    public string $period = '7days';

    protected ?string $heading = 'Traffic Trends: Page Views & Unique Visitors';

    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 2,
    ];

    /**
     * Resolve date bounds for the current active filter period.
     */
    protected function getDateRange(): array
    {
        $now = now();

        return match ($this->period) {
            'today' => [today()->startOfDay(), $now, true],
            'yesterday' => [today()->subDay()->startOfDay(), today()->subDay()->endOfDay(), true],
            '30days' => [now()->subDays(30)->startOfDay(), $now, false],
            'all' => [now()->subYears(2)->startOfDay(), $now, false],
            default => [now()->subDays(7)->startOfDay(), $now, false],
        };
    }

    protected function getData(): array
    {
        [$startDate, $endDate, $isHourly] = $this->getDateRange();

        $labels = [];
        $viewsData = [];
        $visitorsData = [];

        if ($isHourly) {
            // Group by hour of day (0 to 23)
            for ($h = 0; $h <= 23; $h++) {
                $hourStart = (clone $startDate)->hour($h)->minute(0)->second(0);
                $hourEnd = (clone $startDate)->hour($h)->minute(59)->second(59);

                if ($hourStart > $endDate) {
                    break;
                }

                $labels[] = sprintf('%02d:00', $h);

                $viewsData[] = PageView::query()
                    ->forEvent($this->eventId)
                    ->whereBetween('created_at', [$hourStart, $hourEnd])
                    ->count();

                $visitorsData[] = PageView::query()
                    ->forEvent($this->eventId)
                    ->whereBetween('created_at', [$hourStart, $hourEnd])
                    ->distinct('visitor_id')
                    ->count('visitor_id');
            }
        } else {
            // Group by day
            $days = (int) $startDate->diffInDays($endDate);
            for ($i = $days; $i >= 0; $i--) {
                $day = (clone $endDate)->subDays($i)->startOfDay();
                $labels[] = $day->format('M d');

                $viewsData[] = PageView::query()
                    ->forEvent($this->eventId)
                    ->whereDate('created_at', $day)
                    ->count();

                $visitorsData[] = PageView::query()
                    ->forEvent($this->eventId)
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
