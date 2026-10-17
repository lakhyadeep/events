<?php

namespace App\Filament\Widgets;

use App\Models\UserActivity;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentActivityTableWidget extends TableWidget
{
    public ?int $eventId = null;

    public string $period = '7days';

    protected static ?int $sort = 5;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Live Audience Interaction Feed')
            ->query(function (): Builder {
                return UserActivity::query()
                    ->when($this->eventId, fn ($q) => $q->where('event_id', $this->eventId))
                    ->with('event')
                    ->latest('id');
            })
            ->columns([
                TextColumn::make('action_type')
                    ->label('Activity Action')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'search' => 'Keyword Search',
                        'zone_filter' => 'Zone Filtered',
                        'map_directions_click' => 'Map Navigation ("Get Directions")',
                        'vote_initiated' => 'Vote Started (Clicked Vote)',
                        'otp_requested' => 'OTP Dispatched',
                        'vote_completed' => 'Vote Cast & Verified',
                        'banner_click' => 'Sponsor Banner Clicked',
                        'video_play' => 'Video Short Played',
                        default => ucwords(str_replace('_', ' ', $state)),
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'search' => 'heroicon-m-magnifying-glass',
                        'zone_filter' => 'heroicon-m-funnel',
                        'map_directions_click' => 'heroicon-m-map-pin',
                        'vote_initiated' => 'heroicon-m-cursor-arrow-rays',
                        'otp_requested' => 'heroicon-m-device-phone-mobile',
                        'vote_completed' => 'heroicon-m-check-badge',
                        'banner_click' => 'heroicon-m-sparkles',
                        'video_play' => 'heroicon-m-play-circle',
                        default => 'heroicon-m-sparkles',
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'search' => 'info',
                        'zone_filter' => 'gray',
                        'map_directions_click' => 'warning',
                        'vote_initiated' => 'primary',
                        'otp_requested' => 'amber',
                        'vote_completed' => 'success',
                        'banner_click' => 'purple',
                        'video_play' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('metadata')
                    ->label('Interaction Context / Value')
                    ->formatStateUsing(function ($state): string {
                        if (is_array($state)) {
                            if (isset($state['query'])) {
                                return 'Searched: "'.$state['query'].'"';
                            }
                            if (isset($state['puja_name'])) {
                                return 'Puja: '.$state['puja_name'];
                            }
                            if (isset($state['zone'])) {
                                return 'Zone: '.$state['zone'];
                            }
                            if (isset($state['name'])) {
                                return 'Banner: '.$state['name'];
                            }
                            if (isset($state['title'])) {
                                return 'Video: '.$state['title'];
                            }
                            if (isset($state['participant_name'])) {
                                return 'Voted For: '.$state['participant_name'];
                            }

                            return json_encode($state, JSON_UNESCAPED_SLASHES);
                        }

                        return '-';
                    })
                    ->wrap(),

                TextColumn::make('device_type')
                    ->label('Device')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'mobile' => 'warning',
                        'desktop' => 'info',
                        'tablet' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state) => ucfirst($state ?? 'Unknown')),

                TextColumn::make('event.display_name')
                    ->label('Event')
                    ->placeholder('General')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->since()
                    ->description(fn (UserActivity $record) => $record->created_at?->timezone('Asia/Kolkata')->format('d M, h:i:s A'))
                    ->sortable(),
            ])
            ->defaultPaginationPageOption(10)
            ->paginated([5, 10, 25, 50]);
    }
}
