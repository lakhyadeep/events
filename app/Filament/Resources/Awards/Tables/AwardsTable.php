<?php

namespace App\Filament\Resources\Awards\Tables;

use App\Models\Award;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AwardsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('display_name')
                    ->label('Award Title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->color('primary')
                    ->searchable(),
                TextColumn::make('prize_money_or_award')
                    ->label('Prize / Trophy')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('event.display_name')
                    ->label('Event')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('year')
                    ->label('Year')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                SelectFilter::make('event_id')
                    ->label('Event')
                    ->relationship('event', 'display_name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('category')
                    ->options(fn () => Award::distinct()->pluck('category', 'category')->toArray()),
                TernaryFilter::make('is_active')
                    ->label('Active Award'),
                SelectFilter::make('year')
                    ->options(fn () => Award::distinct()->pluck('year', 'year')->toArray()),
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
