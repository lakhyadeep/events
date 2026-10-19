<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AnalyticsDeviceChart;
use App\Filament\Widgets\AnalyticsStatsOverview;
use App\Filament\Widgets\AnalyticsTrafficChart;
use App\Filament\Widgets\PageWiseFunnelWidget;
use App\Filament\Widgets\TopPujasTableWidget;
use App\Models\Event;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Page;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

class Analytics extends Page
{
    use HasFiltersForm;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $filterData = [];

    protected static UnitEnum|string|null $navigationGroup = 'Analytics & Insights';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = 'Audience & Page Analytics';

    protected static ?string $title = 'Audience Behavior & Page Analytics';

    protected string $view = 'filament.pages.analytics';

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Real-time page-wise telemetry, visitor demographics, search keywords, and full voting conversion funnel.';
    }

    public function mount(): void
    {
        $this->mountHasFilters();

        if ($this->filters === null) {
            $this->filters = $this->getFiltersForm()->getState();
        } else {
            $this->getFiltersForm()->fill($this->filters);
        }
    }

    public function applyFilters(): void
    {
        $this->filters = $this->getFiltersForm()->getState();

        if ($this->persistsFiltersInSession()) {
            session()->put(
                $this->getFiltersSessionKey(),
                $this->filters,
            );
        }
    }

    public function resetFilters(): void
    {
        $this->getFiltersForm()->fill([
            'eventId' => null,
            'rangePreset' => null,
            'startDate' => null,
            'endDate' => null,
        ]);
        $this->filters = [
            'eventId' => null,
            'rangePreset' => null,
            'startDate' => null,
            'endDate' => null,
        ];

        if ($this->persistsFiltersInSession()) {
            session()->forget($this->getFiltersSessionKey());
        }
    }

    public function applyFiltersAction(): Action
    {
        return Action::make('applyFilters')
            ->label('Apply Filters')
            ->icon('heroicon-m-funnel')
            ->color('primary')
            ->action(fn () => $this->applyFilters());
    }

    public function clearFiltersAction(): Action
    {
        return Action::make('clearFilters')
            ->label('Reset Filters')
            ->icon('heroicon-m-arrow-path')
            ->color('gray')
            ->action(fn () => $this->resetFilters());
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->live(condition: false)
            ->statePath('filterData')
            ->columns([
                'default' => 1,
                'sm' => 1,
                'md' => 1,
                'lg' => 1,
                'xl' => 1,
                '2xl' => 1,
            ])
            ->components([
                Section::make('Audience & Telemetry Filters')
                    ->description('Filter audience behavior, telemetry, traffic trends, and page-wise conversion funnels by event, preset, or custom datetime range.')
                    ->icon('heroicon-m-funnel')
                    ->collapsible()
                    ->columnSpanFull()
                    ->headerActions([
                        $this->applyFiltersAction(),
                        $this->clearFiltersAction(),
                    ])
                    ->schema([
                        Select::make('eventId')
                            ->label('Event Scope')
                            ->placeholder('All Events (Global)')
                            ->options(fn () => Event::orderByDesc('year')->orderBy('name')->pluck('display_name', 'id')->toArray())
                            ->default(fn () => Event::active()->latest('year')->value('id') ?? Event::latest('year')->value('id'))
                            ->prefixIcon('heroicon-m-calendar')
                            ->searchable()
                            ->live(condition: false),

                        Select::make('rangePreset')
                            ->label('Quick Range Preset')
                            ->placeholder('Custom Range...')
                            ->options([
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
                            ])
                            ->default('last_7_days')
                            ->prefixIcon('heroicon-m-clock')
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                switch ($state) {
                                    case 'last_30_mins':
                                        $set('startDate', now()->subMinutes(30)->format('Y-m-d H:i:s'));
                                        $set('endDate', now()->format('Y-m-d H:i:s'));
                                        break;
                                    case 'last_hour':
                                        $set('startDate', now()->subHour()->format('Y-m-d H:i:s'));
                                        $set('endDate', now()->format('Y-m-d H:i:s'));
                                        break;
                                    case 'last_4_hours':
                                        $set('startDate', now()->subHours(4)->format('Y-m-d H:i:s'));
                                        $set('endDate', now()->format('Y-m-d H:i:s'));
                                        break;
                                    case 'today':
                                        $set('startDate', now()->startOfDay()->format('Y-m-d H:i:s'));
                                        $set('endDate', now()->endOfDay()->format('Y-m-d H:i:s'));
                                        break;
                                    case 'yesterday':
                                        $set('startDate', now()->subDay()->startOfDay()->format('Y-m-d H:i:s'));
                                        $set('endDate', now()->subDay()->endOfDay()->format('Y-m-d H:i:s'));
                                        break;
                                    case 'last_24_hours':
                                        $set('startDate', now()->subHours(24)->format('Y-m-d H:i:s'));
                                        $set('endDate', now()->format('Y-m-d H:i:s'));
                                        break;
                                    case 'last_7_days':
                                        $set('startDate', now()->subDays(7)->startOfDay()->format('Y-m-d H:i:s'));
                                        $set('endDate', now()->endOfDay()->format('Y-m-d H:i:s'));
                                        break;
                                    case 'last_30_days':
                                        $set('startDate', now()->subDays(30)->startOfDay()->format('Y-m-d H:i:s'));
                                        $set('endDate', now()->endOfDay()->format('Y-m-d H:i:s'));
                                        break;
                                    case 'this_month':
                                        $set('startDate', now()->startOfMonth()->format('Y-m-d H:i:s'));
                                        $set('endDate', now()->endOfDay()->format('Y-m-d H:i:s'));
                                        break;
                                    case 'all_time':
                                        $set('startDate', null);
                                        $set('endDate', null);
                                        break;
                                }
                            }),

                        DateTimePicker::make('startDate')
                            ->label('Start Date & Time')
                            ->placeholder('Select start date & time')
                            ->prefixIcon('heroicon-m-calendar')
                            ->native(false)
                            ->seconds(false)
                            ->live(condition: false),

                        DateTimePicker::make('endDate')
                            ->label('End Date & Time')
                            ->placeholder('Select end date & time')
                            ->prefixIcon('heroicon-m-calendar')
                            ->native(false)
                            ->seconds(false)
                            ->minDate(fn (Get $get) => $get('startDate'))
                            ->live(condition: false),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 4,
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFiltersFormContentComponent(),
                $this->getWidgetsContentComponent(),
            ]);
    }

    public function getFiltersFormContentComponent(): Component
    {
        return EmbeddedSchema::make('filtersForm');
    }

    public function getWidgetsContentComponent(): Component
    {
        return Grid::make([
            'default' => 1,
            'lg' => 3,
        ])->schema(fn (): array => $this->getWidgetsSchemaComponents($this->getWidgets()));
    }

    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            AnalyticsStatsOverview::class,
            AnalyticsTrafficChart::class,
            AnalyticsDeviceChart::class,
            PageWiseFunnelWidget::class,
            TopPujasTableWidget::class,
        ];
    }
}
