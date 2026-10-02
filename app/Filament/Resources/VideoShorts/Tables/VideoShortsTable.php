<?php

namespace App\Filament\Resources\VideoShorts\Tables;

use App\Enums\VideoPlatform;
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

class VideoShortsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail_image')
                    ->label('Thumbnail (9:16)')
                    ->width(45)
                    ->height(80)
                    ->circular(false),
                TextColumn::make('name')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->lineClamp(1),
                TextColumn::make('platform')
                    ->badge()
                    ->color(fn ($state): string => match ($state instanceof VideoPlatform ? $state->value : (string) $state) {
                        'youtube_shorts' => 'danger',
                        'instagram' => 'warning',
                        'vimeo' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('event.display_name')
                    ->label('Event')
                    ->searchable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('video_url')
                    ->label('Link')
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('platform')
                    ->label('Platform')
                    ->options(VideoPlatform::options()),
                TernaryFilter::make('is_active')
                    ->label('Active on Stories'),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
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
