<?php

namespace App\Filament\Resources\TimelineItems\Tables;

use App\Enums\TimelineStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TimelineItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Milestone Stage')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('milestone_date')
                    ->label('Scheduled Date / Window')
                    ->searchable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('status')
                    ->label('Progress')
                    ->badge()
                    ->color(fn ($state): string => match ($state instanceof TimelineStatus ? $state->value : (string) $state) {
                        'completed' => 'success',
                        'ongoing' => 'warning',
                        'upcoming' => 'gray',
                        default => 'primary',
                    }),
                TextColumn::make('sort_order')
                    ->label('Stage Order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('event.display_name')
                    ->label('Event')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                SelectFilter::make('event_id')
                    ->label('Event')
                    ->relationship('event', 'display_name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->label('Stage Status')
                    ->options(TimelineStatus::options()),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(2)
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
