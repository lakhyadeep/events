<?php

namespace App\Filament\Resources\Winners\Tables;

use App\Enums\WinnerRank;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WinnersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('rank_order')
                    ->label('Rank')
                    ->badge()
                    ->color(fn ($state): string => match ($state instanceof WinnerRank ? $state->value : (string) $state) {
                        '1st' => 'warning',
                        '2nd' => 'gray',
                        '3rd' => 'info',
                        default => 'success',
                    })
                    ->sortable(),
                TextColumn::make('award.display_name')
                    ->label('Award Trophy')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('participant.display_name')
                    ->label('Winning Candidate / Club')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('event.display_name')
                    ->label('Event')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('year')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state): string => match ((string) ($state instanceof \BackedEnum ? $state->value : $state)) {
                        'published' => 'success',
                        default => 'danger',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('rank_order')
                    ->label('Rank Position')
                    ->options(WinnerRank::options()),
                SelectFilter::make('status')
                    ->label('Podium Status')
                    ->options([
                        'published' => 'Published',
                        'draft' => 'Draft',
                    ]),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
