<?php
/**
 * Database Reset Script - Bookings Only
 * This script will clear booking-related data while preserving user accounts
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Music Studio Database Reset - Bookings Only ===\n";
echo "This will clear booking data while preserving user accounts.\n";
echo "Starting reset process...\n\n";

try {
    // Start transaction for safety
    DB::beginTransaction();
    
    // 1. Backup current user count
    $userCount = DB::table('users')->count();
    echo "Current users in database: {$userCount}\n";
    
    // 2. Clear bookings table
    if (Schema::hasTable('bookings')) {
        $bookingCount = DB::table('bookings')->count();
        echo "Clearing {$bookingCount} booking records...\n";
        DB::table('bookings')->truncate();
        echo "✓ Bookings table cleared\n";
    }
    
    // 3. Clear feedback table (if exists and related to bookings)
    if (Schema::hasTable('feedback')) {
        $feedbackCount = DB::table('feedback')->count();
        echo "Clearing {$feedbackCount} feedback records...\n";
        DB::table('feedback')->truncate();
        echo "✓ Feedback table cleared\n";
    }
    
    // 4. Clear any rental/instrument related tables
    $rentalTables = ['rentals', 'instrument_rentals', 'equipment_rentals'];
    foreach ($rentalTables as $table) {
        if (Schema::hasTable($table)) {
            $count = DB::table($table)->count();
            if ($count > 0) {
                echo "Clearing {$count} records from {$table}...\n";
                DB::table($table)->truncate();
                echo "✓ {$table} table cleared\n";
            }
        }
    }
    
    // 5. Reset auto-increment IDs
    if (Schema::hasTable('bookings')) {
        DB::statement('ALTER TABLE bookings AUTO_INCREMENT = 1');
        echo "✓ Bookings auto-increment reset\n";
    }
    
    if (Schema::hasTable('feedback')) {
        DB::statement('ALTER TABLE feedback AUTO_INCREMENT = 1');
        echo "✓ Feedback auto-increment reset\n";
    }
    
    // 6. Verify users are still intact
    $finalUserCount = DB::table('users')->count();
    echo "\nVerification:\n";
    echo "Users before reset: {$userCount}\n";
    echo "Users after reset: {$finalUserCount}\n";
    
    if ($userCount === $finalUserCount) {
        echo "✓ User accounts preserved successfully\n";
        
        // Commit the transaction
        DB::commit();
        echo "\n=== RESET COMPLETED SUCCESSFULLY ===\n";
        echo "- All booking data cleared\n";
        echo "- All rental/instrument data cleared\n";
        echo "- User accounts preserved\n";
        echo "- Analytics will be regenerated on next access\n";
        
    } else {
        throw new Exception("User count mismatch! Rolling back changes.");
    }
    
} catch (Exception $e) {
    // Rollback on any error
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "All changes have been rolled back.\n";
    exit(1);
}

echo "\nDatabase reset completed. You can now start fresh with bookings!\n";
?>