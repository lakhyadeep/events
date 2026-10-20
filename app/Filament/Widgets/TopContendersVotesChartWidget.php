<?php

namespace App\Filament\Widgets;

use App\Models\Award;
use App\Models\Participant;
use App\Models\Vote;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class TopContendersVotesChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): ?string
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

            return "Top 10 Contenders: Vote Count by Award Category (Filtered: {$formattedStart} - {$formattedEnd})";
        }

        return 'Top 10 Contenders: Vote Count by Award Category';
    }

    public function getDescription(): ?string
    {
        return 'Distribution of verified public votes across each official award category for the top contenders.';
    }

    protected function getData(): array
    {
        $startDate = ! empty($this->pageFilters['startDate'] ?? null)
            ? rescue(fn () => Carbon::parse($this->pageFilters['startDate']), null, false)
            : null;

        $endDate = ! empty($this->pageFilters['endDate'] ?? null)
            ? rescue(fn () => Carbon::parse($this->pageFilters['endDate']), null, false)
            : null;

        $topContenders = Participant::query()
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
            ->get();

        if ($topContenders->isEmpty()) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $contenderIds = $topContenders->pluck('id')->all();

        $awards = Award::query()
            ->where('is_active', true)
            ->where('is_voting_allowed', true)
            ->where('name', 'not like', '%Best Puja%')
            ->orderBy('sort_order')
            ->get();

        $groupedVotes = Vote::getCountsByParticipantAndAward($contenderIds, $startDate, $endDate)
            ->groupBy('participant_id');

        $labels = $topContenders->map(function ($contender) {
            $name = $contender->display_name ?: $contender->name;

            return mb_strlen($name) > 24 ? mb_substr($name, 0, 22).'…' : $name;
        })->all();

        $palette = [
            ['bg' => '#f59e0b', 'border' => '#d97706'], // Amber
            ['bg' => '#10b981', 'border' => '#059669'], // Emerald
            ['bg' => '#6366f1', 'border' => '#4f46e5'], // Indigo
            ['bg' => '#0ea5e9', 'border' => '#0284c7'], // Sky
            ['bg' => '#ec4899', 'border' => '#db2777'], // Pink
            ['bg' => '#8b5cf6', 'border' => '#7c3aed'], // Violet
            ['bg' => '#f43f5e', 'border' => '#e11d48'], // Rose
        ];

        $datasets = [];
        $colorIndex = 0;

        foreach ($awards as $award) {
            $data = [];
            foreach ($topContenders as $contender) {
                $contenderVotes = $groupedVotes->get($contender->id);
                $voteRow = $contenderVotes ? $contenderVotes->firstWhere('award_id', $award->id) : null;
                $data[] = $voteRow ? (int) $voteRow->votes_count : 0;
            }

            $color = $palette[$colorIndex % count($palette)];
            $colorIndex++;

            $datasets[] = [
                'label' => $award->display_name ?: $award->name,
                'data' => $data,
                'backgroundColor' => $color['bg'],
                'borderColor' => $color['border'],
                'borderWidth' => 1,
                'borderRadius' => 4,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected ?string $maxHeight = '420px';

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 16,
                    ],
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'stacked' => true,
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
                'y' => [
                    'stacked' => true,
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }
}
