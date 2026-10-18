<?php

namespace App\Filament\Resources\Votes\Tables;

use App\Filament\Resources\Votes\Pages\ListVotes;
use App\Models\Vote;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
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
                        if (strlen($digits) >= 10) {
                            return '+91 ' . substr($digits, 0, 5) . '*****';
                        }

                        return $state;
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

                TextColumn::make('fraud_flag')
                    ->label('Integrity Status')
                    ->state(fn(Vote $record): string => $record->fraud_assessment['label'])
                    ->badge()
                    ->color(fn(Vote $record): string => $record->fraud_assessment['color'])
                    ->icon(fn(Vote $record): string => $record->fraud_assessment['is_fraud'] ? 'heroicon-o-shield-exclamation' : 'heroicon-o-shield-check')
                    ->description(fn(Vote $record): string => $record->fraud_assessment['reason'])
                    ->toggleable(),

                TextColumn::make('participant.display_name')
                    ->label('Voted For (Puja / Candidate)')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->badge()
                    ->color('danger')
                    ->toggleable(),

                TextColumn::make('award.display_name')
                    ->label('Award Category')
                    ->badge()
                    ->color('primary')
                    ->placeholder('Overall People\'s Choice')
                    ->sortable(),

                TextColumn::make('event.display_name')
                    ->label('Event')
                    ->sortable()
                    ->toggleable(),

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
                    ->preload(),

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

                SelectFilter::make('fraud_status')
                    ->label('Integrity Status')
                    ->options([
                        'flagged' => 'Flagged as Fraud / Suspicious',
                        'genuine' => 'Verified Genuine Only',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (($data['value'] ?? null) === 'flagged') {
                            $disposable = config('fraud.disposable_numbers', []);
                            $cached = (array) Cache::get('fraud_disposable_blacklist', []);
                            $allBlocked = array_values(array_unique(array_merge($disposable, $cached)));

                            return $query->where(function (Builder $sub) use ($allBlocked) {
                                $sub->whereRaw('LENGTH(mobile_number) != 10')
                                    ->orWhereRaw("SUBSTR(mobile_number, 1, 1) NOT IN ('6', '7', '8', '9')")
                                    ->when(!empty($allBlocked), fn($q) => $q->orWhereIn('mobile_number', $allBlocked));
                            });
                        }

                        if (($data['value'] ?? null) === 'genuine') {
                            $disposable = config('fraud.disposable_numbers', []);
                            $cached = (array) Cache::get('fraud_disposable_blacklist', []);
                            $allBlocked = array_values(array_unique(array_merge($disposable, $cached)));

                            return $query->whereRaw('LENGTH(mobile_number) = 10')
                                ->whereRaw("SUBSTR(mobile_number, 1, 1) IN ('6', '7', '8', '9')")
                                ->when(!empty($allBlocked), fn($q) => $q->whereNotIn('mobile_number', $allBlocked));
                        }

                        return $query;
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
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(5)
            ->recordActions([
                ViewAction::make(),
                Action::make('same_ip_votes')
                    ->label('Same IP Votes')
                    ->icon('heroicon-o-globe-alt')
                    ->color('warning')
                    ->tooltip('View all votes submitted from this IP address')
                    ->visible(fn(Vote $record): bool => !empty($record->ip_address) && $record->same_ip_votes_count > 1)
                    ->url(fn(Vote $record): string => ListVotes::getUrl(['tableSearch' => $record->ip_address])),
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
