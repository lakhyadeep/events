<?php

namespace App\Filament\Resources\Votes\RelationManagers;

use App\Models\Otp;
use BackedEnum;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OtpsRelationManager extends RelationManager
{
    protected static string $relationship = 'otps';

    protected static ?string $title = 'Unverified SMS OTP Trail';

    protected static string|BackedEnum|null $icon = 'heroicon-o-shield-exclamation';

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        $count = $ownerRecord->otps()->whereNull('verified_at')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getBadgeColor(Model $ownerRecord, string $pageClass): ?string
    {
        return 'warning';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('phone')
            ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('verified_at'))
            ->emptyStateHeading('No unverified OTPs')
            ->emptyStateDescription('All dispatched OTPs for this number were successfully verified or none exist.')
            ->emptyStateIcon('heroicon-o-check-badge')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Dispatched At')
                    ->dateTime('d M Y, h:i:s A')
                    ->timezone('Asia/Kolkata')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(function (Otp $record): string {
                        if ($record->isExpired()) {
                            return 'Expired';
                        }

                        return 'Pending / Unverified';
                    })
                    ->color(function (Otp $record): string {
                        if ($record->isExpired()) {
                            return 'danger';
                        }

                        return 'warning';
                    })
                    ->icon(function (Otp $record): string {
                        if ($record->isExpired()) {
                            return 'heroicon-o-x-circle';
                        }

                        return 'heroicon-o-clock';
                    }),

                TextColumn::make('expires_at')
                    ->label('Expires At')
                    ->dateTime('d M Y, h:i:s A')
                    ->timezone('Asia/Kolkata')
                    ->sortable(),

                TextColumn::make('attempts')
                    ->label('Attempts')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'gray')
                    ->formatStateUsing(fn (int $state): string => $state === 0 ? '0 failed' : "{$state} failed"),

                TextColumn::make('ip_address')
                    ->label('Request IP')
                    ->fontFamily('mono')
                    ->placeholder('Unknown')
                    ->copyable(),

                TextColumn::make('purpose')
                    ->label('Purpose')
                    ->badge()
                    ->color('info')
                    ->placeholder('vote_verification'),
            ])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
