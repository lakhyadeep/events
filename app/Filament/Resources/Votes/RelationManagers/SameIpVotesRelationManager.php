<?php

namespace App\Filament\Resources\Votes\RelationManagers;

use App\Filament\Resources\Votes\VoteResource;
use App\Models\Vote;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SameIpVotesRelationManager extends RelationManager
{
    protected static string $relationship = 'sameIpVotes';

    protected static ?string $title = 'Votes from Same IP Address';

    protected static string|BackedEnum|null $icon = 'heroicon-o-globe-alt';

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        $count = $ownerRecord->sameIpVotes()->whereKeyNot($ownerRecord->getKey())->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getBadgeColor(Model $ownerRecord, string $pageClass): ?string
    {
        return 'warning';
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('voter_name')
            ->recordUrl(fn (Vote $record): string => VoteResource::getUrl('view', ['record' => $record]))
            ->modifyQueryUsing(fn (Builder $query) => $query->whereKeyNot($this->getOwnerRecord()->getKey()))
            ->emptyStateHeading('No other votes from this IP')
            ->emptyStateDescription('This is the only vote recorded from this IP address.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('voter_name')
                    ->label('Voter Name')
                    ->placeholder('Anonymous')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->url(fn (Vote $record): string => VoteResource::getUrl('view', ['record' => $record])),

                TextColumn::make('mobile_number')
                    ->label('Voter Mobile')
                    ->formatStateUsing(function (string $state): string {
                        $digits = preg_replace('/[^0-9]/', '', $state) ?? '';
                        if (strlen($digits) >= 10) {
                            return '+91 '.substr($digits, 0, 5).'*****';
                        }

                        return $state;
                    })
                    ->searchable()
                    ->copyable()
                    ->copyableState(fn (Vote $record): string => '+91 '.$record->mobile_number)
                    ->copyMessage('Mobile number copied to clipboard'),

                TextColumn::make('participant.display_name')
                    ->label('Voted For (Puja)')
                    ->weight('semibold')
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('award.display_name')
                    ->label('Award Category')
                    ->placeholder('Overall People\'s Choice')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('device_name')
                    ->label('Device')
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-o-device-phone-mobile'),

                TextColumn::make('created_at')
                    ->label('Cast At')
                    ->dateTime('d M Y, h:i:s A')
                    ->timezone('Asia/Kolkata')
                    ->sortable(),
            ])
            ->headerActions([])
            ->recordActions([
                Action::make('view')
                    ->label('View Vote')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (Vote $record): string => VoteResource::getUrl('view', ['record' => $record])),
                DeleteAction::make()
                    ->label('Void Vote')
                    ->modalHeading('Void Vote')
                    ->modalDescription('Are you sure you want to void this vote? This action will permanently remove the vote from the official tally.'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Void Selected Votes')
                        ->modalHeading('Void Selected Votes')
                        ->modalDescription('Are you sure you want to void all selected votes? This action cannot be undone.'),
                ]),
            ]);
    }
}
