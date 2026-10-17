<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\ChartWidget;

class AnalyticsDeviceChart extends ChartWidget
{
    public ?int $eventId = null;

    public string $period = '7days';

    protected ?string $heading = 'Audience Devices & Form Factors';

    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 1,
    ];

    /**
     * Resolve date bounds for current period.
     */
    protected function getDateRange(): array
    {
        $now = now();

        return match ($this->period) {
            'today' => [today()->startOfDay(), $now],
            'yesterday' => [today()->subDay()->startOfDay(), today()->subDay()->endOfDay()],
            '30days' => [now()->subDays(30)->startOfDay(), $now],
            'all' => [now()->subYears(2)->startOfDay(), $now],
            default => [now()->subDays(7)->startOfDay(), $now],
        };
    }

    protected function getData(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $query = PageView::query()
            ->forEvent($this->eventId)
            ->whereBetween('created_at', [$startDate, $endDate]);

        $mobileCount = (clone $query)->where('device_type', 'mobile')->count();
        $desktopCount = (clone $query)->where('device_type', 'desktop')->count();
        $tabletCount = (clone $query)->where('device_type', 'tablet')->count();

        // Default to a small placeholder count if brand new
        if ($mobileCount === 0 && $desktopCount === 0 && $tabletCount === 0) {
            $mobileCount = 1;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Devices',
                    'data' => [$mobileCount, $desktopCount, $tabletCount],
                    'backgroundColor' => [
                        '#10b981', // Emerald
                        '#6366f1', // Indigo
                        '#f59e0b', // Amber
                    ],
                    'hoverOffset' => 6,
                ],
            ],
            'labels' => [
                "Mobile ({$mobileCount})",
                "Desktop ({$desktopCount})",
                "Tablet ({$tabletCount})",
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
