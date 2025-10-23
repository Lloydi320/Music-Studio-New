<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Schedule automatic backups
        $schedule->command('backup:create --auto')
                 ->daily()
                 ->at('02:00')
                 ->description('Create automatic daily backup');
                 
        // Weekly backup cleanup (keep only last 4 weeks)
        $schedule->call(function () {
            $backupDir = storage_path('app/backups');
            if (\File::exists($backupDir)) {
                $files = \File::files($backupDir);
                $autoBackups = collect($files)
                    ->filter(function ($file) {
                        return str_contains($file->getFilename(), 'auto_backup_');
                    })
                    ->sortBy(function ($file) {
                        return $file->getMTime();
                    });
                
                // Keep only the last 28 auto backups (4 weeks)
                if ($autoBackups->count() > 28) {
                    $filesToDelete = $autoBackups->take($autoBackups->count() - 28);
                    foreach ($filesToDelete as $file) {
                        \File::delete($file->getPathname());
                    }
                }
            }
        })->weekly()->description('Clean up old automatic backups');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}