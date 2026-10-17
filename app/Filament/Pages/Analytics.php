<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AnalyticsDeviceChart;
use App\Filament\Widgets\AnalyticsStatsOverview;
use App\Filament\Widgets\AnalyticsTrafficChart;
use App\Filament\Widgets\RecentActivityTableWidget;
use App\Filament\Widgets\TopPujasTableWidget;
use App\Models\Event;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Url;
use UnitEnum;

class Analytics extends Page
{
    protected static UnitEnum|string|null $navigationGroup = 'Analytics & Insights';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = 'Audience & Page Analytics';

    protected static ?string $title = 'Audience Behavior & Page Analytics';

    protected string $view = 'filament.pages.analytics';

    #[Url]
    public ?int $eventId = null;

    #[Url]
    public string $period = '7days';

    public function mount(): void
    {
        // Default to active event if not set in URL
        if ($this->eventId === null) {
            $activeEvent = Event::where('status', 'active')->latest('year')->first();
            $this->eventId = $activeEvent?->id;
        }
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Real-time telemetry, visitor demographics, search keywords, map directions, and voting conversion funnel.';
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    public function getEventsProperty()
    {
        return Event::orderByDesc('year')->orderBy('name')->pluck('display_name', 'id')->toArray();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AnalyticsStatsOverview::class,
            AnalyticsTrafficChart::class,
            AnalyticsDeviceChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return [
            'default' => 1,
            'lg' => 3,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            TopPujasTableWidget::class,
            RecentActivityTableWidget::class,
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 1;
    }

    /**
     * Pass active filters to all rendered widgets.
     *
     * @return array<string, mixed>
     */
    public function getWidgetData(): array
    {
        return [
            'eventId' => $this->eventId,
            'period' => $this->period,
        ];
    }
}
