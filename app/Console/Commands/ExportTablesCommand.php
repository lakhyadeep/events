<?php

namespace App\Console\Commands;

use App\Services\TableExportService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

#[Signature('db:export {--path= : Custom directory to store exported CSV files (defaults to exports/)} {--table=* : Specific table(s) to export (participants, votes, winners)}')]
#[Description('Export participants, votes, and winners database tables to CSV format with relation IDs replaced with actual names')]
class ExportTablesCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(TableExportService $exportService): int
    {
        $customPath = $this->option('path');
        $outputDir = $customPath ? (string) $customPath : base_path('exports');

        $tables = (array) $this->option('table');
        if (empty($tables)) {
            $tables = ['participants', 'votes', 'winners'];
        } else {
            $tables = array_map('strtolower', $tables);
        }

        $this->info("Starting export to directory: {$outputDir}");
        $this->newLine();

        $summary = [];

        try {
            foreach ($tables as $table) {
                $filePath = $outputDir.DIRECTORY_SEPARATOR."{$table}.csv";

                $this->output->write("Exporting <comment>{$table}</comment>... ");

                $count = match ($table) {
                    'participants' => $exportService->exportParticipants($filePath),
                    'votes' => $exportService->exportVotes($filePath),
                    'winners' => $exportService->exportWinners($filePath),
                    default => null,
                };

                if ($count === null) {
                    $this->error("Unknown table '{$table}'. Supported tables: participants, votes, winners.");

                    continue;
                }

                $fileSize = file_exists($filePath) ? round(filesize($filePath) / 1024, 2).' KB' : '0 KB';
                $this->info("done ({$count} records, {$fileSize})");

                $summary[] = [
                    'Table' => $table,
                    'Records' => number_format($count),
                    'Filename' => basename($filePath),
                    'Size' => $fileSize,
                    'Full Path' => $filePath,
                ];
            }

            $this->newLine();
            $this->info('Export completed successfully!');
            $this->table(['Table', 'Records', 'Filename', 'Size', 'Full Path'], $summary);

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->newLine();
            $this->error('Export failed: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
