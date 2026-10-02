<?php

namespace App\Filament\Resources\Participants\Tables;

use App\Enums\RegistrationStatus;
use App\Enums\Zone;
use Filament\Actions\BulkAction;
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
use Illuminate\Database\Eloquent\Collection;

class ParticipantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('primary_display_image')
                    ->label('Photo')
                    ->circular()
                    ->size(40),
                TextColumn::make('display_name')
                    ->label('Club / Candidate')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->locality),
                TextColumn::make('event.display_name')
                    ->label('Event')
                    ->sortable(),
                TextColumn::make('zone')
                    ->label('Zone')
                    ->badge()
                    ->sortable(),
                TextColumn::make('registration_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state): string => match ($state instanceof RegistrationStatus ? $state->value : $state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
                IconColumn::make('is_shortlisted')
                    ->label('Featured')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('mobile_number')
                    ->label('Phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('key_contact_1_name')
                    ->label('Contact')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('idol_artist')
                    ->label('Idol Artist')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('theme_artist')
                    ->label('Theme Artist')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('is_shortlisted', 'desc')
            ->filters([
                SelectFilter::make('event_id')
                    ->label('Event')
                    ->relationship('event', 'display_name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('zone')
                    ->label('Zone')
                    ->options(Zone::options()),
                SelectFilter::make('registration_status')
                    ->label('Registration Status')
                    ->options(RegistrationStatus::options()),
                TernaryFilter::make('is_shortlisted')
                    ->label('Shortlisted Only'),
                TernaryFilter::make('is_puja_contest')
                    ->label('Puja Festival Entries'),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(5)
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['registration_status' => RegistrationStatus::Approved])),
                    BulkAction::make('bulk_shortlist')
                        ->label('Mark Shortlisted')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->action(fn (Collection $records) => $records->each->update(['is_shortlisted' => true])),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
