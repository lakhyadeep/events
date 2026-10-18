<?php

namespace App\Filament\Resources\Votes\Pages;

use App\Filament\Resources\Votes\VoteResource;
use App\Models\Vote;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListVotes extends ListRecords
{
    protected static string $resource = VoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_csv')
                ->label('Export Votes (CSV)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function (): StreamedResponse {
                    $filename = 'votes_'.now()->format('Ymd_His').'.csv';

                    return response()->streamDownload(function () {
                        $handle = fopen('php://output', 'w');
                        fputcsv($handle, ['ID', 'Event', 'Candidate / Committee', 'Award Category', 'Voter Name', 'Mobile Number', 'IP Address', 'Device', 'Cast At']);

                        Vote::with(['event', 'participant', 'award'])
                            ->latest('id')
                            ->chunk(500, function ($votes) use ($handle) {
                                foreach ($votes as $vote) {
                                    fputcsv($handle, [
                                        $vote->id,
                                        $vote->event?->display_name ?? 'N/A',
                                        $vote->participant?->display_name ?: ($vote->participant?->name ?? 'N/A'),
                                        $vote->award?->display_name ?: ($vote->award?->name ?? 'Overall Event Vote'),
                                        $vote->voter_name ?: 'Anonymous',
                                        '+91 '.$vote->mobile_number,
                                        $vote->ip_address ?? 'N/A',
                                        $vote->device_name,
                                        $vote->created_at?->toDateTimeString() ?? '',
                                    ]);
                                }
                            });

                        fclose($handle);
                    }, $filename, [
                        'Content-Type' => 'text/csv',
                    ]);
                }),
        ];
    }
}
