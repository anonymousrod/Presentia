<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Enums\ActivityStatus;
use Illuminate\Console\Command;

class ArchiveOldActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activities:archive-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive activities that ended more than 90 days ago';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cutoffDate = now()->subDays(90);

        $affected = Activity::where('end_time', '<', $cutoffDate)
            ->where('status', '!=', ActivityStatus::ARCHIVED->value)
            ->update(['status' => ActivityStatus::ARCHIVED->value]);

        $this->info("Archived {$affected} old activities successfully.");
    }
}
