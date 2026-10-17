<?php

namespace App\Console\Commands;

use App\Models\PageView;
use App\Models\UserActivity;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('analytics:prune {--days=90 : The number of days of analytics logs to retain}')]
#[Description('Prune obsolete page views and user activity tracking records.')]
class PruneAnalyticsData extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $this->info("Pruning analytics logs older than {$days} days (before {$cutoff->toDateTimeString()})...");

        $deletedViews = PageView::where('created_at', '<', $cutoff)->delete();
        $this->line("Deleted <comment>{$deletedViews}</comment> obsolete page view logs.");

        $deletedActivities = UserActivity::where('created_at', '<', $cutoff)->delete();
        $this->line("Deleted <comment>{$deletedActivities}</comment> obsolete user activity logs.");

        $this->info('Analytics pruning completed successfully.');

        return self::SUCCESS;
    }
}
