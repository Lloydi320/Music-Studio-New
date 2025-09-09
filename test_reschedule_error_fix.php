<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\InstrumentRental;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

echo "Testing Reschedule Error Fix...\n";
echo "================================\n\n";

// Test 1: Check ActivityLog table structure
echo "Test 1: Checking ActivityLog table structure...\n";
try {
    $columns = DB::select("DESCRIBE activity_logs");
    echo "✓ ActivityLog table exists with columns:\n";
    foreach ($columns as $column) {
        echo "  - {$column->Field} ({$column->Type})\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking ActivityLog table: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 2: Test ActivityLog::logActivity method
echo "Test 2: Testing ActivityLog::logActivity method...\n";
try {
    $testLog = ActivityLog::logActivity(
        'Test reschedule log entry',
        'test_action',
        1,
        'App\\Models\\InstrumentRental',
        1,
        ['old' => 'data'],
        ['new' => 'data'],
        ActivityLog::SEVERITY_MEDIUM
    );
    
    if ($testLog) {
        echo "✓ ActivityLog::logActivity works correctly\n";
        echo "  Created log ID: {$testLog->id}\n";
        
        // Clean up test log
        $testLog->delete();
        echo "  Test log cleaned up\n";
    } else {
        echo "✗ ActivityLog::logActivity returned null\n";
    }
} catch (Exception $e) {
    echo "✗ Error testing ActivityLog::logActivity: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Check for existing instrument rentals
echo "Test 3: Checking for existing instrument rentals...\n";
try {
    $rentals = InstrumentRental::whereIn('status', ['pending', 'confirmed'])->take(3)->get();
    echo "✓ Found " . $rentals->count() . " active rentals\n";
    
    foreach ($rentals as $rental) {
        echo "  - Rental {$rental->four_digit_code}: {$rental->instrument_type} ({$rental->status})\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking rentals: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Simulate reschedule data creation
echo "Test 4: Testing reschedule data structure...\n";
try {
    $rental = InstrumentRental::whereIn('status', ['pending', 'confirmed'])->first();
    
    if ($rental) {
        $rescheduleData = [
            'rental_id' => $rental->id,
            'booking_type' => 'instrument_rental',
            'old_start_date' => $rental->rental_start_date->format('Y-m-d'),
            'old_end_date' => $rental->rental_end_date->format('Y-m-d'),
            'old_duration' => $rental->rental_duration_days,
            'new_start_date' => '2025-09-15',
            'new_end_date' => '2025-09-17',
            'new_duration' => 3,
            'requested_by' => $rental->user_id,
            'requested_at' => now()
        ];
        
        echo "✓ Reschedule data structure created successfully\n";
        echo "  Rental ID: {$rescheduleData['rental_id']}\n";
        echo "  Old dates: {$rescheduleData['old_start_date']} to {$rescheduleData['old_end_date']}\n";
        echo "  New dates: {$rescheduleData['new_start_date']} to {$rescheduleData['new_end_date']}\n";
        
        // Test the actual logging that was causing issues
        echo "\nTesting ActivityLog creation with reschedule data...\n";
        $logEntry = ActivityLog::logActivity(
            'Test Reschedule Request: Instrument Rental',
            'rental_reschedule_requested',
            $rental->user_id,
            'App\\Models\\InstrumentRental',
            $rental->id,
            $rental->toArray(),
            $rescheduleData,
            ActivityLog::SEVERITY_MEDIUM
        );
        
        if ($logEntry) {
            echo "✓ ActivityLog created successfully for reschedule\n";
            echo "  Log ID: {$logEntry->id}\n";
            echo "  Description: {$logEntry->description}\n";
            
            // Clean up
            $logEntry->delete();
            echo "  Test log cleaned up\n";
        }
        
    } else {
        echo "✗ No active rentals found for testing\n";
    }
} catch (Exception $e) {
    echo "✗ Error testing reschedule data: " . $e->getMessage() . "\n";
}
echo "\n";

echo "Test Summary:\n";
echo "=============\n";
echo "The reschedule error fix has been tested.\n";
echo "If all tests passed, the SQLSTATE error should be resolved.\n";
echo "The issue was caused by incorrect ActivityLog field mapping.\n";
echo "Now using ActivityLog::logActivity() method for proper field handling.\n";