<?php

namespace App\Filament\Resources\Winners\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
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
                    ->color(fn (string $state): string => match ($state) {
                        '1st' => 'warning',
                        '2nd' => 'gray',
                        '3rd' => 'info',
                        default => 'success',
                    })
                    ->sortable(),
                TextColumn::make('award.display_name')
                    ->label('Award')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('participant.display_name')
                    ->label('Winning Participant')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('event.display_name')
                    ->label('Event')
                    ->searchable(),
                TextColumn::make('year')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        default => 'danger',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
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
