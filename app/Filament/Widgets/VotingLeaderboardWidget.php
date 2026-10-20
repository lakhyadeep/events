<?php

namespace App\Filament\Widgets;

use App\Models\Participant;
use Carbon\Carbon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;

class VotingLeaderboardWidget extends TableWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 4;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public function getTableHeading(): ?string
    {
        $startDate = ! empty($this->pageFilters['startDate'] ?? null)
            ? rescue(fn () => Carbon::parse($this->pageFilters['startDate']), null, false)
            : null;

        $endDate = ! empty($this->pageFilters['endDate'] ?? null)
            ? rescue(fn () => Carbon::parse($this->pageFilters['endDate']), null, false)
            : null;

        if ($startDate || $endDate) {
            $formattedStart = $startDate ? $startDate->format('M d, H:i') : 'Start';
            $formattedEnd = $endDate ? $endDate->format('M d, H:i') : 'Now';

            return "Live Public Voting Leaderboard (Filtered: {$formattedStart} - {$formattedEnd})";
        }

        return 'Live Public Voting Leaderboard (Top 10 Contenders)';
    }

    public function table(Table $table): Table
    {
        $startDate = ! empty($this->pageFilters['startDate'] ?? null)
            ? rescue(fn () => Carbon::parse($this->pageFilters['startDate']), null, false)
            : null;

        $endDate = ! empty($this->pageFilters['endDate'] ?? null)
            ? rescue(fn () => Carbon::parse($this->pageFilters['endDate']), null, false)
            : null;

        return $table
            ->query(
                Participant::query()
                    ->with(['event', 'zone', 'locality'])
                    ->withCount(['votes' => function ($query) use ($startDate, $endDate) {
                        if ($startDate) {
                            $query->where('created_at', '>=', $startDate);
                        }
                        if ($endDate) {
                            $query->where('created_at', '<=', $endDate);
                        }
                    }])
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

                TextColumn::make('award_breakdown')
                    ->label('Award Category Breakdown')
                    ->html()
                    ->state(function (Participant $record) use ($startDate, $endDate): string {
                        $counts = $record->getVoteCountsByAward($startDate, $endDate)->where('votes_count', '>', 0);
                        if ($counts->isEmpty()) {
                            return '<span class="text-xs text-zinc-400 dark:text-zinc-500 font-normal">0 category votes</span>';
                        }

                        return $counts->map(function ($item) {
                            $label = e($item['display_name']);
                            $count = (int) $item['votes_count'];

                            return "<span class=\"inline-flex items-center gap-1 rounded-md bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-900/50 px-2 py-0.5 text-xs font-semibold text-amber-900 dark:text-amber-200 me-1 mb-1 shadow-xs\">{$label}: <strong class=\"text-rose-600 dark:text-rose-400 font-extrabold\">{$count}</strong></span>";
                        })->implode('');
                    }),
            ])
            ->paginated(false);
    }
}
