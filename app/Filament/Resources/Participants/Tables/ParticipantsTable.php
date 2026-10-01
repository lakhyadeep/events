<?php

namespace App\Filament\Resources\Participants\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ParticipantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('primary_display_image')
                    ->label('Photo')
                    ->circular(),
                TextColumn::make('display_name')
                    ->label('Candidate / Club')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('zone')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('locality')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('puja_theme')
                    ->label('Theme')
                    ->searchable()
                    ->limit(25),
                TextColumn::make('registration_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
                IconColumn::make('is_shortlisted')
                    ->label('Shortlisted')
                    ->boolean()
                    ->sortable(),
                IconColumn::make('is_puja_contest')
                    ->label('Puja Entry')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('mobile_number')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('key_contact_1_name')
                    ->label('Contact Person')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('idol_artist')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('event.display_name')
                    ->label('Event')
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
