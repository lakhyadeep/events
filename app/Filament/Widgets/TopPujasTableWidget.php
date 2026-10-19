<?php

namespace App\Filament\Widgets;

use App\Models\Participant;
use App\Models\UserActivity;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class TopPujasTableWidget extends TableWidget
{
    use InteractsWithPageFilters;

    public ?int $eventId = null;

    public string $period = 'last_7_days';

    protected static ?int $sort = 4;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected ?array $voteClicksCache = null;

    #[On('analytics-filter-updated')]
    public function updateFilters(?int $eventId, string $period): void
    {
        $this->eventId = $eventId ?: null;
        $this->period = $period;
        $this->voteClicksCache = null;
        $this->resetTable();
    }

    public function updatedPageFilters(): void
    {
        $this->voteClicksCache = null;
        $this->resetTable();
    }

    protected function getActiveEventId(): ?int
    {
        $id = $this->pageFilters['eventId'] ?? $this->eventId ?? null;

        return $id ? (int) $id : null;
    }

    /**
     * @return array{0: ?Carbon, 1: ?Carbon}
     */
    protected function getDateRanges(): array
    {
        $startDate = ! empty($this->pageFilters['startDate'] ?? null)
            ? rescue(fn () => Carbon::parse($this->pageFilters['startDate']), null, false)
            : null;

        $endDate = ! empty($this->pageFilters['endDate'] ?? null)
            ? rescue(fn () => Carbon::parse($this->pageFilters['endDate']), null, false)
            : null;

        if ($startDate || $endDate) {
            return [
                $startDate ?? now()->subDays(7)->startOfDay(),
                $endDate ?? now(),
            ];
        }

        $now = now();
        $preset = $this->pageFilters['rangePreset'] ?? $this->period ?? 'last_7_days';

        return match ($preset) {
            'last_30_mins', '30m', '30mins' => [$now->clone()->subMinutes(30), $now],
            'last_hour', '1h', '1hour' => [$now->clone()->subHour(), $now],
            'last_4_hours', '4h', '4hours' => [$now->clone()->subHours(4), $now],
            'today' => [today()->startOfDay(), $now],
            'yesterday' => [today()->subDay()->startOfDay(), today()->subDay()->endOfDay()],
            'last_24_hours', '24h', '24hours' => [$now->clone()->subHours(24), $now],
            'last_7_days', '7days' => [now()->subDays(7)->startOfDay(), $now],
            'last_30_days', '30days' => [now()->subDays(30)->startOfDay(), $now],
            'this_month' => [now()->startOfMonth(), $now],
            'all_time', 'all' => [null, null],
            default => [now()->subDays(7)->startOfDay(), $now],
        };
    }

    protected function getVoteClicks(int $participantId): int
    {
        if ($this->voteClicksCache === null) {
            $eventId = $this->getActiveEventId();
            [$startDate, $endDate] = $this->getDateRanges();

            $this->voteClicksCache = UserActivity::query()
                ->when($eventId, fn ($q) => $q->where('event_id', $eventId))
                ->when($startDate && $endDate, fn ($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
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
                $eventId = $this->getActiveEventId();
                [$startDate, $endDate] = $this->getDateRanges();

                return Participant::query()
                    ->when($eventId, fn ($q) => $q->where('event_id', $eventId))
                    ->withCount([
                        'votes' => fn ($q) => $q->when($startDate && $endDate, fn ($sub) => $sub->whereBetween('created_at', [$startDate, $endDate])),
                    ])
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
