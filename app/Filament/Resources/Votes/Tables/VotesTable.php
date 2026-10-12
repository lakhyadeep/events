<?php

namespace App\Filament\Resources\Votes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VotesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
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
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->recordActions([
                ViewAction::make(),
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
