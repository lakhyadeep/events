<?php

namespace App\Filament\Resources\Votes\Tables;

use App\Filament\Resources\Votes\VoteResource;
use App\Models\Event;
use App\Models\Vote;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class VotesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('voter_name')
                    ->label('Voter Name')
                    ->placeholder('Anonymous')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('mobile_number')
                    ->label('Voter Mobile')
                    ->formatStateUsing(function (string $state): string {
                        $digits = preg_replace('/[^0-9]/', '', $state) ?? '';

                        return strlen($digits) === 10 ? '+91 ' . $digits : $state;
                    })
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyableState(fn($record) => '+91 ' . $record->mobile_number)
                    ->copyMessage('Mobile number copied to clipboard')
                    ->description(function (Vote $record): string {
                        $ip = $record->ip_address ?? 'Unknown';
                        $count = $record->same_ip_votes_count;

                        return $count > 1 ? "IP: {$ip} (⚠️ {$count} votes)" : "IP: {$ip}";
                    }),

                // TextColumn::make('fraud_flag')
                //     ->label('Integrity Status')
                //     ->state(fn (Vote $record): string => $record->fraud_assessment['label'])
                //     ->badge()
                //     ->color(fn (Vote $record): string => $record->fraud_assessment['color'])
                //     ->icon(fn (Vote $record): string => $record->fraud_assessment['is_fraud'] ? 'heroicon-o-shield-exclamation' : 'heroicon-o-shield-check')
                //     ->description(fn (Vote $record): string => $record->fraud_assessment['reason'])
                //     ->toggleable(),

                TextColumn::make('participant.display_name')
                    ->label('Voted For')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->badge()
                    ->color('danger')
                    ->limit(20)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        return is_string($state) && mb_strlen($state) > 20 ? $state : null;
                    })
                    ->wrap()
                    ->grow(false)
                    ->toggleable(),

                TextColumn::make('award.display_name')
                    ->label('Award Category')
                    ->badge()
                    ->color('primary')
                    ->placeholder('Overall People\'s Choice')
                    ->sortable(),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->description(function (Vote $record): ?string {
                        $count = $record->same_ip_votes_count;

                        return $count > 1 ? "⚠️ {$count} votes from this IP" : null;
                    }),

                TextColumn::make('created_at')
                    ->label('Cast At')
                    ->dateTime('d M Y, h:i A')
                    ->timezone('Asia/Kolkata')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('event_id')
                    ->label('Event')
                    ->relationship('event', 'display_name')
                    ->searchable()
                    ->preload()
                    ->default(fn() => Event::where('status', 'active')->latest('year')->value('id') ?? Event::latest('year')->value('id')),

                SelectFilter::make('award_id')
                    ->label('Award Category')
                    ->relationship('award', 'display_name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('participant_id')
                    ->label('Candidate / Committee')
                    ->relationship('participant', 'display_name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('time_window')
                    ->label('Quick Interval Presets')
                    ->placeholder('All Time')
                    ->options([
                        '15_mins' => 'Last 15 Minutes',
                        '30_mins' => 'Last 30 Minutes',
                        '1_hour' => 'Last 1 Hour (Hourly)',
                        '3_hours' => 'Last 3 Hours',
                        '6_hours' => 'Last 6 Hours',
                        '12_hours' => 'Last 12 Hours',
                        'today' => 'Today',
                        'yesterday' => 'Yesterday',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            '15_mins' => $query->where('created_at', '>=', now()->subMinutes(15)),
                            '30_mins' => $query->where('created_at', '>=', now()->subMinutes(30)),
                            '1_hour' => $query->where('created_at', '>=', now()->subHour()),
                            '3_hours' => $query->where('created_at', '>=', now()->subHours(3)),
                            '6_hours' => $query->where('created_at', '>=', now()->subHours(6)),
                            '12_hours' => $query->where('created_at', '>=', now()->subHours(12)),
                            'today' => $query->where('created_at', '>=', today()->startOfDay()),
                            'yesterday' => $query->whereBetween('created_at', [
                                today()->subDay()->startOfDay(),
                                today()->subDay()->endOfDay(),
                            ]),
                            default => $query,
                        };
                    }),

                Filter::make('date_time_range')
                    ->label('Custom Date & Time Range')
                    ->form([
                        DateTimePicker::make('from')
                            ->label('From Date & Time')
                            ->seconds(false)
                            ->timezone('Asia/Kolkata')
                            ->placeholder('Start date & time'),
                        DateTimePicker::make('until')
                            ->label('Until Date & Time')
                            ->seconds(false)
                            ->timezone('Asia/Kolkata')
                            ->placeholder('End date & time'),
                    ])
                    ->columns(['default' => 1, 'sm' => 2])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn(Builder $q, $date) => $q->where('created_at', '>=', Carbon::parse($date)),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn(Builder $q, $date) => $q->where('created_at', '<=', Carbon::parse($date)),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if (!empty($data['from'])) {
                            $indicators['from'] = 'From ' . Carbon::parse($data['from'])->timezone('Asia/Kolkata')->format('d M, h:i A');
                        }
                        if (!empty($data['until'])) {
                            $indicators['until'] = 'Until ' . Carbon::parse($data['until'])->timezone('Asia/Kolkata')->format('d M, h:i A');
                        }

                        return $indicators;
                    }),

                SelectFilter::make('same_ip')
                    ->label('Same IP / Cluster')
                    ->placeholder('All IP Activity')
                    ->options([
                        'shared' => 'Multiple Votes from Same IP (Shared IP)',
                        'unique' => 'Single Vote per IP (Unique IP)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (($data['value'] ?? null) === 'shared') {
                            return $query->whereIn('ip_address', function ($sub): void {
                                $sub->select('ip_address')
                                    ->from('votes')
                                    ->whereNotNull('ip_address')
                                    ->where('ip_address', '!=', '')
                                    ->groupBy('ip_address')
                                    ->havingRaw('COUNT(*) > 1');
                            });
                        }

                        if (($data['value'] ?? null) === 'unique') {
                            return $query->whereIn('ip_address', function ($sub): void {
                                $sub->select('ip_address')
                                    ->from('votes')
                                    ->whereNotNull('ip_address')
                                    ->where('ip_address', '!=', '')
                                    ->groupBy('ip_address')
                                    ->havingRaw('COUNT(*) = 1');
                            });
                        }

                        return $query;
                    }),

                Filter::make('same_ip_count')
                    ->label('Same IP Count (> N)')
                    ->form([
                        TextInput::make('min_count')
                            ->label('Same IP Count > N')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('e.g. 2, 5, 10...')
                            ->helperText('Filter votes where IP has more than N votes'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $min = (int) ($data['min_count'] ?? 0);
                        if ($min <= 0) {
                            return $query;
                        }

                        return $query->whereIn('ip_address', function ($sub) use ($min): void {
                            $sub->select('ip_address')
                                ->from('votes')
                                ->whereNotNull('ip_address')
                                ->where('ip_address', '!=', '')
                                ->groupBy('ip_address')
                                ->havingRaw('COUNT(*) > ' . (int) $min);
                        });
                    })
                    ->indicateUsing(function (array $data): ?string {
                        $min = (int) ($data['min_count'] ?? 0);
                        if ($min <= 0) {
                            return null;
                        }

                        return "Same IP Count > {$min}";
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(12)
            ->filtersFormSchema(fn(array $filters): array => [
                Fieldset::make('Contest Scope')
                    ->schema([
                        $filters['event_id'],
                        $filters['award_id'],
                        $filters['participant_id'],
                    ])
                    ->columns(['default' => 1, 'sm' => 3])
                    ->columnSpan(['default' => 12, 'xl' => 7]),

                Fieldset::make('IP Forensics & Traffic')
                    ->schema([
                        $filters['same_ip'],
                        $filters['same_ip_count'],
                    ])
                    ->columns(['default' => 1, 'sm' => 2])
                    ->columnSpan(['default' => 12, 'xl' => 5]),

                Fieldset::make('Date & Time Window')
                    ->schema([
                        $filters['time_window']
                            ->columnSpan(['default' => 1, 'lg' => 1]),
                        $filters['date_time_range']
                            ->columnSpan(['default' => 1, 'lg' => 2]),
                    ])
                    ->columns(['default' => 1, 'lg' => 3])
                    ->columnSpan(12),
            ])
            ->recordUrl(fn(Vote $record): string => VoteResource::getUrl('view', ['record' => $record]))
            ->recordActions([
                ViewAction::make(),
                Action::make('same_ip_votes')
                    ->label('Same IP Votes')
                    ->icon('heroicon-o-globe-alt')
                    ->color('warning')
                    ->tooltip('View all votes submitted from this IP address')
                    ->visible(fn(Vote $record): bool => !empty($record->ip_address) && $record->same_ip_votes_count > 1)
                    ->url(fn(Vote $record): string => VoteResource::getUrl('view', ['record' => $record])),
                Action::make('blacklist')
                    ->label('Blacklist Number')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Blacklist Mobile Number')
                    ->modalDescription('Block this number from ever receiving OTPs or submitting votes in the future.')
                    ->action(function (Vote $record): void {
                        $cached = (array) Cache::get('fraud_disposable_blacklist', []);
                        if (!in_array($record->mobile_number, $cached, true)) {
                            $cached[] = $record->mobile_number;
                            Cache::put('fraud_disposable_blacklist', $cached, now()->addYears(1));
                        }

                        Notification::make()
                            ->title('Number Blacklisted')
                            ->body("Mobile number +91 {$record->mobile_number} has been blocked from voting.")
                            ->danger()
                            ->send();
                    }),
                DeleteAction::make()
                    ->label('Void Vote')
                    ->modalHeading('Void Vote')
                    ->modalDescription('Are you sure you want to void this vote? This action will permanently remove the vote from the official tally.'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Void Selected Votes'),
                ]),
            ]);
    }
}
