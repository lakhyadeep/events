<?php

namespace App\Filament\Resources\Sponsors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SponsorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->height(40),
                TextColumn::make('display_name')
                    ->label('Sponsor')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('sponsor_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'presenting_partner' => 'warning',
                        'associate_sponsor' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('slot_order')
                    ->label('Slot (1-5)')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('sponsor_tag')
                    ->label('Tagline')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('event.display_name')
                    ->label('Event')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('landing_url')
                    ->label('Website')
                    ->limit(25)
                    ->toggleable(isToggledHiddenByDefault: true),
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
