<?php

namespace App\Filament\Widgets;

use App\Models\Participant;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class VotingLeaderboardWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Live Public Voting Leaderboard (Top 10 Contenders)';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Participant::query()
                    ->with(['event', 'zone', 'locality'])
                    ->withCount('votes')
                    ->orderByDesc('votes_count')
                    ->orderByDesc('is_shortlisted')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('rank')
                    ->rowIndex()
                    ->label('#')
                    ->badge()
                    ->color(fn ($rowLoop): string => match ($rowLoop->iteration) {
                        1 => 'warning',
                        2 => 'gray',
                        3 => 'danger',
                        default => 'info',
                    }),

                ImageColumn::make('primary_display_image')
                    ->label('Photo')
                    ->circular()
                    ->size(36),

                TextColumn::make('display_name')
                    ->label('Puja Committee / Candidate')
                    ->weight('bold')
                    ->description(fn ($record) => $record->locality ? $record->locality.', '.$record->zone : ($record->zone ?: 'Dibrugarh')),

                TextColumn::make('event.display_name')
                    ->label('Event')
                    ->toggleable(),

                TextColumn::make('zone')
                    ->label('Zone')
                    ->badge(),

                TextColumn::make('votes_count')
                    ->label('Verified Votes')
                    ->badge()
                    ->color('danger')
                    ->weight('bold')
                    ->icon('heroicon-m-hand-thumb-up')
                    ->formatStateUsing(fn ($state) => number_format((int) $state).' votes'),
            ])
            ->paginated(false);
    }
}
