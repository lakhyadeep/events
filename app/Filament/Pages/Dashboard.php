<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('clearFilters')
                ->label('Reset Filters')
                ->icon('heroicon-m-arrow-path')
                ->color('gray')
                ->action(function (): void {
                    $this->filters = null;
                    $this->getFiltersForm()->fill([]);
                }),
        ];
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->columns([
                'default' => 1,
                'sm' => 1,
                'md' => 1,
                'lg' => 1,
                'xl' => 1,
                '2xl' => 1,
            ])
            ->components([
                Section::make('Date & Time Filters')
                    ->description('Filter dashboard metrics, live voting stats, SMS usage, and leaderboard rankings by custom date and time.')
                    ->icon('heroicon-m-calendar-days')
                    ->collapsible()
                    ->columnSpanFull()
                    ->headerActions([
                        Action::make('clearFilters')
                            ->label('Reset Filters')
                            ->icon('heroicon-m-arrow-path')
                            ->color('gray')
                            ->action(function (): void {
                                $this->filters = null;
                                $this->getFiltersForm()->fill([]);
                            }),
                    ])
                    ->schema([
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
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('rangePreset', null)),

                        DateTimePicker::make('endDate')
                            ->label('End Date & Time')
                            ->placeholder('Select end date & time')
                            ->prefixIcon('heroicon-m-calendar')
                            ->native(false)
                            ->seconds(false)
                            ->minDate(fn (Get $get) => $get('startDate'))
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('rangePreset', null)),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ]),
            ]);
    }
}
