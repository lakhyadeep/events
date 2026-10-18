<?php

namespace App\Filament\Resources\Votes\Tables;

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
                            return '+91 '.substr($digits, 0, 5).'*****';
                        }

                        return $state;
                    })
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyableState(fn ($record) => '+91 '.$record->mobile_number)
                    ->copyMessage('Mobile number copied to clipboard')
                    ->description(fn ($record) => 'IP: '.($record->ip_address ?? 'Unknown')),

                TextColumn::make('fraud_flag')
                    ->label('Integrity Status')
                    ->state(fn (Vote $record): string => $record->fraud_assessment['label'])
                    ->badge()
                    ->color(fn (Vote $record): string => $record->fraud_assessment['color'])
                    ->icon(fn (Vote $record): string => $record->fraud_assessment['is_fraud'] ? 'heroicon-o-shield-exclamation' : 'heroicon-o-shield-check')
                    ->description(fn (Vote $record): string => $record->fraud_assessment['reason']),

                TextColumn::make('participant.display_name')
                    ->label('Voted For (Puja / Candidate)')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->badge()
                    ->color('danger'),

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
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Cast At')
                    ->dateTime('d M Y, h:i A')
                    ->timezone('Asia/Kolkata')
                    ->sortable(),
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
                                    ->when(! empty($allBlocked), fn ($q) => $q->orWhereIn('mobile_number', $allBlocked));
                            });
                        }

                        if (($data['value'] ?? null) === 'genuine') {
                            $disposable = config('fraud.disposable_numbers', []);
                            $cached = (array) Cache::get('fraud_disposable_blacklist', []);
                            $allBlocked = array_values(array_unique(array_merge($disposable, $cached)));

                            return $query->whereRaw('LENGTH(mobile_number) = 10')
                                ->whereRaw("SUBSTR(mobile_number, 1, 1) IN ('6', '7', '8', '9')")
                                ->when(! empty($allBlocked), fn ($q) => $q->whereNotIn('mobile_number', $allBlocked));
                        }

                        return $query;
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->recordActions([
                ViewAction::make(),
                Action::make('blacklist')
                    ->label('Blacklist Number')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Blacklist Mobile Number')
                    ->modalDescription('Block this number from ever receiving OTPs or submitting votes in the future.')
                    ->action(function (Vote $record): void {
                        $cached = (array) Cache::get('fraud_disposable_blacklist', []);
                        if (! in_array($record->mobile_number, $cached, true)) {
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
