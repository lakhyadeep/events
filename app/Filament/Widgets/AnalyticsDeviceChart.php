<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Livewire\Attributes\On;

class AnalyticsDeviceChart extends ChartWidget
{
    use InteractsWithPageFilters;

    public ?int $eventId = null;

    public string $period = 'last_7_days';

    protected ?string $heading = 'Audience Devices & Form Factors';

    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 1,
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
     * Resolve date bounds for current period.
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
            'all_time', 'all' => [now()->subYears(2)->startOfDay(), $now],
            default => [now()->subDays(7)->startOfDay(), $now],
        };
    }

    protected function getData(): array
    {
        [$startDate, $endDate] = $this->getDateRange();
        $eventId = $this->getActiveEventId();

        $query = PageView::query()
            ->forEvent($eventId)
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
