<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Activitylog\Models\Activity;

class PruneAuditLog extends Command
{
    protected $signature = 'hk:audit:prune {--days=180 : Delete entries older than this many days} {--dry-run : Show what would be deleted}';

    protected $description = 'Prune the audit/activity log of entries older than the given age.';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $cutoff = now()->subDays($days);

        $query = Activity::where('created_at', '<', $cutoff);
        $count = $query->count();

        if ($this->option('dry-run')) {
            $this->info("Would delete {$count} activity log entries older than {$days} days (before {$cutoff->toDateTimeString()}).");

            return self::SUCCESS;
        }

        $deleted = $query->delete();
        $this->info("Deleted {$deleted} activity log entries older than {$days} days.");

        return self::SUCCESS;
    }
}
