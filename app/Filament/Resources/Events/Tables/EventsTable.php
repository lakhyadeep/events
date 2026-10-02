<?php

namespace App\Filament\Resources\Events\Tables;

use App\Enums\EventStatus;
use App\Models\Event;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('display_name')
                    ->label('Event Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('year')
                    ->label('Year')
                    ->sortable()
                    ->badge(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable()
                    ->color('gray'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state): string => match ($state instanceof EventStatus ? $state->value : (string) $state) {
                        'active' => 'success',
                        'draft' => 'warning',
                        'archived' => 'gray',
                        default => 'primary',
                    }),
                IconColumn::make('is_voting_active')
                    ->label('Voting')
                    ->boolean(),
                IconColumn::make('is_registration_active')
                    ->label('Registration')
                    ->boolean(),
                IconColumn::make('is_awards_active')
                    ->label('Awards')
                    ->boolean(),
                IconColumn::make('is_timeline_active')
                    ->label('Timeline')
                    ->boolean(),
                TextColumn::make('countdown_datetime')
                    ->label('Target Date')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('contact_email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(EventStatus::options()),
                SelectFilter::make('year')
                    ->options(fn () => Event::distinct()->pluck('year', 'year')->toArray()),
                TernaryFilter::make('is_voting_active')
                    ->label('Voting Open'),
                TernaryFilter::make('is_registration_active')
                    ->label('Registration Open'),
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
