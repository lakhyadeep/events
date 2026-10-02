<?php

namespace App\Filament\Resources\Sponsors\Tables;

use App\Enums\SponsorType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SponsorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('Brand Logo')
                    ->height(36),
                TextColumn::make('display_name')
                    ->label('Brand')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('sponsor_type')
                    ->label('Tier')
                    ->badge()
                    ->color(fn ($state): string => match ($state instanceof SponsorType ? $state->value : (string) $state) {
                        'presenting_partner' => 'warning',
                        'associate_sponsor' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('slot_order')
                    ->label('Slot (1-5)')
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->alignCenter(),
                TextColumn::make('sponsor_tag')
                    ->label('Tagline / Designation')
                    ->searchable()
                    ->color('gray'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('event.display_name')
                    ->label('Event')
                    ->searchable()
                    ->badge()
                    ->toggleable(),
                TextColumn::make('landing_url')
                    ->label('URL')
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('slot_order', 'asc')
            ->filters([
                SelectFilter::make('event_id')
                    ->label('Event')
                    ->relationship('event', 'display_name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('sponsor_type')
                    ->label('Sponsor Tier')
                    ->options(SponsorType::options()),
                SelectFilter::make('slot_order')
                    ->label('Slot Position')
                    ->options([
                        1 => 'Slot 1',
                        2 => 'Slot 2',
                        3 => 'Slot 3',
                        4 => 'Slot 4',
                        5 => 'Slot 5',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Active on Masthead'),
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
