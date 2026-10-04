<?php

namespace App\Filament\Widgets;

use App\Models\Participant;
use App\Models\TimelineItem;
use App\Models\VideoShort;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContestActivityStats extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $data = cache()->remember('filament_contest_activity_stats_data', now()->addSeconds(30), function (): array {
            $pujaEntries = Participant::where('is_puja_contest', true)->count();
            $shortlistedCandidates = Participant::where('is_shortlisted', true)->count();
            $activeShorts = VideoShort::where('is_active', true)->count();
            $completedTimeline = TimelineItem::where('status', 'completed')->count();
            $totalTimeline = TimelineItem::count();

            return [
                'pujaEntries' => $pujaEntries,
                'shortlistedCandidates' => $shortlistedCandidates,
                'activeShorts' => $activeShorts,
                'completedTimeline' => $completedTimeline,
                'totalTimeline' => $totalTimeline,
            ];
        });

        return [
            Stat::make('Puja Contest Entries', $data['pujaEntries'])
                ->description('Durga Puja cultural committees')
                ->descriptionIcon('heroicon-m-building-library')
                ->color('primary')
                ->chart([2, 4, 6, 8, $data['pujaEntries']]),

            Stat::make('Shortlisted Contenders', $data['shortlistedCandidates'])
                ->description('Featured on public showcase')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning')
                ->chart([1, 2, 4, $data['shortlistedCandidates']]),

            Stat::make('Video Shorts Reels', $data['activeShorts'])
                ->description('9:16 vertical video stories active')
                ->descriptionIcon('heroicon-m-video-camera')
                ->color('danger')
                ->chart([1, 2, 3, $data['activeShorts']]),

            Stat::make('Timeline Milestones', "{$data['completedTimeline']} / {$data['totalTimeline']}")
                ->description('Event schedule progress')
                ->descriptionIcon('heroicon-m-flag')
                ->color('success')
                ->chart([1, 2, 2, $data['completedTimeline']]),
        ];
    }
}
