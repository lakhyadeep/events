<?php

namespace App\Filament\Widgets;

use App\Models\Participant;
use App\Models\UserActivity;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class TopPujasTableWidget extends TableWidget
{
    public ?int $eventId = null;

    public string $period = '7days';

    protected static ?int $sort = 4;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected ?array $voteClicksCache = null;

    protected function getVoteClicks(int $participantId): int
    {
        if ($this->voteClicksCache === null) {
            $this->voteClicksCache = UserActivity::query()
                ->when($this->eventId, fn ($q) => $q->where('event_id', $this->eventId))
                ->where('target_type', 'participant')
                ->where('action_type', 'vote_initiated')
                ->groupBy('target_id')
                ->selectRaw('target_id, count(*) as total')
                ->pluck('total', 'target_id')
                ->all();
        }

        return $this->voteClicksCache[$participantId] ?? 0;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Pujas Engagement Leaderboard: Votes & Conversions')
            ->query(function (): Builder {
                return Participant::query()
                    ->when($this->eventId, fn ($q) => $q->where('event_id', $this->eventId))
                    ->withCount('votes')
                    ->orderByDesc('votes_count');
            })
            ->columns([
                TextColumn::make('name')
                    ->label('Puja Committee / Pandal')
                    ->description(fn (Participant $record) => $record->locality ? "{$record->locality}, {$record->zone}" : ($record->zone ?? ''))
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('zone')
                    ->label('Zone')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('vote_clicks')
                    ->label('Votes Initiated')
                    ->state(fn (Participant $record): int => max($this->getVoteClicks($record->id), (int) ($record->votes_count ?? 0)))
                    ->icon('heroicon-m-device-phone-mobile')
                    ->badge()
                    ->color('amber'),

                TextColumn::make('votes_count')
                    ->label('Verified Votes')
                    ->icon('heroicon-m-check-badge')
                    ->badge()
                    ->color('success')
                    ->sortable(),
            ])
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10, 25]);
    }
}
