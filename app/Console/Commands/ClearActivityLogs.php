<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ActivityLog;

class ClearActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clear {--keep-recent=0 : Number of recent logs to keep}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear activity logs, optionally keeping recent entries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $keepRecent = (int) $this->option('keep-recent');
        
        if ($keepRecent > 0) {
            // Keep only the most recent entries
            $recentIds = ActivityLog::orderBy('created_at', 'desc')
                ->limit($keepRecent)
                ->pluck('id');
            
            $deleted = ActivityLog::whereNotIn('id', $recentIds)->delete();
            
            $this->info("Cleared {$deleted} activity log entries, kept {$keepRecent} recent entries.");
        } else {
            // Clear all logs
            $deleted = ActivityLog::truncate();
            $this->info('All activity logs have been cleared.');
        }
        
        return 0;
    }
}