<?php

namespace App\Services;

use App\Enums\WinnerRank;
use App\Models\Locality;
use App\Models\Participant;
use App\Models\Vote;
use App\Models\Winner;
use App\Models\Zone;
use Illuminate\Support\Facades\File;
use RuntimeException;

class TableExportService
{
    /**
     * Export participants table to CSV with relation IDs replaced with actual names.
     */
    public function exportParticipants(string $destinationPath): int
    {
        $this->ensureDirectoryExists($destinationPath);

        $handle = fopen($destinationPath, 'w');
        if ($handle === false) {
            throw new RuntimeException("Failed to open file for writing: {$destinationPath}");
        }

        // Write UTF-8 BOM for Microsoft Excel compatibility
        fwrite($handle, "\xEF\xBB\xBF");

        $headers = [
            'id',
            'event_name',
            'name',
            'display_name',
            'mobile_number',
            'zone',
            'locality',
            'address',
            'landmark',
            'short_introduction',
            'key_contact_1_name',
            'key_contact_1_phone',
            'key_contact_2_name',
            'key_contact_2_phone',
            'year',
            'registration_status',
            'is_shortlisted',
            'is_puja_contest',
            'first_year_of_puja',
            'puja_theme',
            'sound_designer',
            'light_designer',
            'idol_artist',
            'theme_artist',
            'google_map_url',
            'primary_display_image',
            'image_1',
            'image_2',
            'image_3',
            'concept_note_image',
            'created_at',
            'updated_at',
        ];

        fputcsv($handle, $headers);

        $zones = Zone::all()->keyBy('id');
        $localities = Locality::all()->keyBy('id');

        $count = 0;
        Participant::query()
            ->with(['event'])
            ->orderBy('id')
            ->chunk(500, function ($participants) use ($handle, $zones, $localities, &$count) {
                foreach ($participants as $p) {
                    $zoneName = $p->zone ?: ($zones->get($p->zone_id)?->name ?? '');
                    $localityName = $p->locality ?: ($localities->get($p->locality_id)?->name ?? '');
                    $status = $p->registration_status instanceof \BackedEnum
                        ? $p->registration_status->value
                        : (string) $p->registration_status;

                    fputcsv($handle, [
                        $p->id,
                        $p->event?->name ?? '',
                        $p->name ?? '',
                        $p->display_name ?? '',
                        $p->mobile_number ?? '',
                        $zoneName,
                        $localityName,
                        $p->address ?? '',
                        $p->landmark ?? '',
                        $p->short_introduction ?? '',
                        $p->key_contact_1_name ?? '',
                        $p->key_contact_1_phone ?? '',
                        $p->key_contact_2_name ?? '',
                        $p->key_contact_2_phone ?? '',
                        $p->year ?? '',
                        $status,
                        $p->is_shortlisted ? 'Yes' : 'No',
                        $p->is_puja_contest ? 'Yes' : 'No',
                        $p->first_year_of_puja ?? '',
                        $p->puja_theme ?? '',
                        $p->sound_designer ?? '',
                        $p->light_designer ?? '',
                        $p->idol_artist ?? '',
                        $p->theme_artist ?? '',
                        $p->google_map_url ?? '',
                        $p->primary_display_image ?? '',
                        $p->image_1 ?? '',
                        $p->image_2 ?? '',
                        $p->image_3 ?? '',
                        $p->concept_note_image ?? '',
                        $p->created_at?->toDateTimeString() ?? '',
                        $p->updated_at?->toDateTimeString() ?? '',
                    ]);
                    $count++;
                }
            });

        fclose($handle);

        return $count;
    }

    /**
     * Export votes table to CSV with relation IDs replaced with actual names.
     */
    public function exportVotes(string $destinationPath): int
    {
        $this->ensureDirectoryExists($destinationPath);

        $handle = fopen($destinationPath, 'w');
        if ($handle === false) {
            throw new RuntimeException("Failed to open file for writing: {$destinationPath}");
        }

        // Write UTF-8 BOM for Microsoft Excel compatibility
        fwrite($handle, "\xEF\xBB\xBF");

        $headers = [
            'id',
            'event_name',
            'participant_name',
            'award_name',
            'award_category',
            'voter_name',
            'mobile_number',
            'ip_address',
            'user_agent',
            'created_at',
            'updated_at',
        ];

        fputcsv($handle, $headers);

        $count = 0;
        Vote::query()
            ->with(['event', 'participant', 'award'])
            ->orderBy('id')
            ->chunk(1000, function ($votes) use ($handle, &$count) {
                foreach ($votes as $v) {
                    $participantName = $v->participant?->display_name ?: ($v->participant?->name ?? 'Unknown Participant');
                    $awardName = $v->award?->display_name ?: ($v->award?->name ?? 'General / Overall');
                    $awardCategory = $v->award?->category ?? '';

                    fputcsv($handle, [
                        $v->id,
                        $v->event?->name ?? '',
                        $participantName,
                        $awardName,
                        $awardCategory,
                        $v->voter_name ?? '',
                        $v->mobile_number ?? '',
                        $v->ip_address ?? '',
                        $v->user_agent ?? '',
                        $v->created_at?->toDateTimeString() ?? '',
                        $v->updated_at?->toDateTimeString() ?? '',
                    ]);
                    $count++;
                }
            });

        fclose($handle);

        return $count;
    }

    /**
     * Export winners table to CSV with relation IDs replaced with actual names.
     */
    public function exportWinners(string $destinationPath): int
    {
        $this->ensureDirectoryExists($destinationPath);

        $handle = fopen($destinationPath, 'w');
        if ($handle === false) {
            throw new RuntimeException("Failed to open file for writing: {$destinationPath}");
        }

        // Write UTF-8 BOM for Microsoft Excel compatibility
        fwrite($handle, "\xEF\xBB\xBF");

        $headers = [
            'id',
            'event_name',
            'award_name',
            'award_category',
            'participant_name',
            'year',
            'rank_order',
            'rank_title',
            'status',
            'created_at',
            'updated_at',
        ];

        fputcsv($handle, $headers);

        $count = 0;
        Winner::query()
            ->with(['event', 'award', 'participant'])
            ->orderBy('id')
            ->chunk(500, function ($winners) use ($handle, &$count) {
                foreach ($winners as $w) {
                    $participantName = $w->participant?->display_name ?: ($w->participant?->name ?? 'Unknown Participant');
                    $awardName = $w->award?->display_name ?: ($w->award?->name ?? 'Unknown Award');
                    $awardCategory = $w->award?->category ?? '';
                    $rankOrder = $w->rank_order instanceof WinnerRank ? $w->rank_order->value : ($w->rank_order ?? '');
                    $rankTitle = $w->rank_order instanceof WinnerRank ? $w->rank_order->label() : ($w->rank_order ?? '');

                    fputcsv($handle, [
                        $w->id,
                        $w->event?->name ?? '',
                        $awardName,
                        $awardCategory,
                        $participantName,
                        $w->year ?? '',
                        $rankOrder,
                        $rankTitle,
                        $w->status ?? '',
                        $w->created_at?->toDateTimeString() ?? '',
                        $w->updated_at?->toDateTimeString() ?? '',
                    ]);
                    $count++;
                }
            });

        fclose($handle);

        return $count;
    }

    /**
     * Export all three tables to a directory.
     *
     * @return array<string, array{file: string, count: int, size: string}>
     */
    public function exportAll(string $outputDir): array
    {
        $this->ensureDirectoryExists($outputDir.DIRECTORY_SEPARATOR.'placeholder.tmp');

        $files = [
            'participants' => $outputDir.DIRECTORY_SEPARATOR.'participants.csv',
            'votes' => $outputDir.DIRECTORY_SEPARATOR.'votes.csv',
            'winners' => $outputDir.DIRECTORY_SEPARATOR.'winners.csv',
        ];

        $results = [];

        $pCount = $this->exportParticipants($files['participants']);
        $results['participants'] = [
            'file' => $files['participants'],
            'count' => $pCount,
            'size' => $this->formatFileSize($files['participants']),
        ];

        $vCount = $this->exportVotes($files['votes']);
        $results['votes'] = [
            'file' => $files['votes'],
            'count' => $vCount,
            'size' => $this->formatFileSize($files['votes']),
        ];

        $wCount = $this->exportWinners($files['winners']);
        $results['winners'] = [
            'file' => $files['winners'],
            'count' => $wCount,
            'size' => $this->formatFileSize($files['winners']),
        ];

        return $results;
    }

    protected function ensureDirectoryExists(string $filePath): void
    {
        $dir = dirname($filePath);
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
    }

    protected function formatFileSize(string $filePath): string
    {
        if (! file_exists($filePath)) {
            return '0 B';
        }

        $bytes = filesize($filePath);
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2).' '.$units[$i];
    }
}
