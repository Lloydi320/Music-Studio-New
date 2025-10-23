<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:reset {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all booking and rental data while preserving user accounts';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🎵 Music Studio Database Reset Tool');
        $this->info('This will clear all booking and rental data while preserving user accounts.');
        $this->newLine();

        // Get current counts
        $userCount = DB::table('users')->count();
        $bookingCount = Schema::hasTable('bookings') ? DB::table('bookings')->count() : 0;
        $feedbackCount = Schema::hasTable('feedback') ? DB::table('feedback')->count() : 0;

        $this->table(['Table', 'Current Records'], [
            ['Users', $userCount . ' (will be preserved)'],
            ['Bookings', $bookingCount . ' (will be deleted)'],
            ['Feedback', $feedbackCount . ' (will be deleted)'],
        ]);

        // Confirmation
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to reset all booking data?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Starting database reset...');
        $this->newLine();

        try {
            DB::beginTransaction();

            // Clear bookings table
            if (Schema::hasTable('bookings') && $bookingCount > 0) {
                DB::table('bookings')->truncate();
                $this->info("✓ Cleared {$bookingCount} booking records");
            }

            // Clear feedback table
            if (Schema::hasTable('feedback') && $feedbackCount > 0) {
                DB::table('feedback')->truncate();
                $this->info("✓ Cleared {$feedbackCount} feedback records");
            }

            // Clear rental tables
            $rentalTables = ['rentals', 'instrument_rentals', 'equipment_rentals'];
            foreach ($rentalTables as $table) {
                if (Schema::hasTable($table)) {
                    $count = DB::table($table)->count();
                    if ($count > 0) {
                        DB::table($table)->truncate();
                        $this->info("✓ Cleared {$count} records from {$table}");
                    }
                }
            }

            // Reset auto-increment IDs
            if (Schema::hasTable('bookings')) {
                DB::statement('ALTER TABLE bookings AUTO_INCREMENT = 1');
                $this->info('✓ Reset bookings auto-increment');
            }

            if (Schema::hasTable('feedback')) {
                DB::statement('ALTER TABLE feedback AUTO_INCREMENT = 1');
                $this->info('✓ Reset feedback auto-increment');
            }

            // Verify users are still intact
            $finalUserCount = DB::table('users')->count();
            
            if ($userCount === $finalUserCount) {
                DB::commit();
                
                $this->newLine();
                $this->info('✅ RESET COMPLETED SUCCESSFULLY!');
                $this->info("- All booking data cleared");
                $this->info("- User accounts preserved ({$finalUserCount} users)");
                $this->info("- Ready for fresh bookings");
                
                // Clear Laravel cache
                $this->info('Clearing Laravel cache...');
                $this->call('cache:clear');
                $this->call('config:clear');
                $this->call('view:clear');
                $this->info('✓ Cache cleared');
                
                return 0;
                
            } else {
                throw new \Exception("User count mismatch! Expected {$userCount}, got {$finalUserCount}");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ ERROR: ' . $e->getMessage());
            $this->error('All changes have been rolled back.');
            return 1;
        }
    }
}