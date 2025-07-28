<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\Feedback;
use App\Models\User;

echo "=== Database Tables Investigation ===\n\n";

try {
    // Check database connection
    echo "1. Database Connection Test:\n";
    $connection = DB::connection();
    $databaseName = $connection->getDatabaseName();
    echo "   ✅ Connected to database: {$databaseName}\n\n";
    
    // Check if tables exist
    echo "2. Table Existence Check:\n";
    $tables = ['users', 'bookings', 'feedback', 'admin_users', 'instrument_rentals'];
    
    foreach ($tables as $table) {
        $exists = DB::getSchemaBuilder()->hasTable($table);
        echo "   - {$table}: " . ($exists ? '✅ EXISTS' : '❌ MISSING') . "\n";
    }
    echo "\n";
    
    // Check bookings table
    echo "3. Bookings Table Analysis:\n";
    if (DB::getSchemaBuilder()->hasTable('bookings')) {
        $totalBookings = Booking::count();
        echo "   Total bookings: {$totalBookings}\n";
        
        if ($totalBookings > 0) {
            echo "   Recent bookings:\n";
            $recentBookings = Booking::with('user')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            
            foreach ($recentBookings as $booking) {
                $userName = $booking->user ? $booking->user->name : 'Unknown User';
                echo "     - ID: {$booking->id}, Reference: {$booking->reference}, Date: {$booking->date}, Status: {$booking->status}, User: {$userName}\n";
            }
        } else {
            echo "   ⚠️  No bookings found in database\n";
        }
        
        // Check table structure
        echo "   \n   Bookings table columns:\n";
        $columns = DB::getSchemaBuilder()->getColumnListing('bookings');
        foreach ($columns as $column) {
            echo "     - {$column}\n";
        }
    } else {
        echo "   ❌ Bookings table does not exist\n";
    }
    echo "\n";
    
    // Check feedback table
    echo "4. Feedback Table Analysis:\n";
    if (DB::getSchemaBuilder()->hasTable('feedback')) {
        $totalFeedback = Feedback::count();
        echo "   Total feedback: {$totalFeedback}\n";
        
        if ($totalFeedback > 0) {
            echo "   Recent feedback:\n";
            $recentFeedback = Feedback::orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            
            foreach ($recentFeedback as $feedback) {
                echo "     - ID: {$feedback->id}, Name: {$feedback->name}, Rating: {$feedback->rating}, Comment: " . substr($feedback->comment, 0, 50) . "...\n";
            }
        } else {
            echo "   ⚠️  No feedback found in database\n";
        }
        
        // Check table structure
        echo "   \n   Feedback table columns:\n";
        $columns = DB::getSchemaBuilder()->getColumnListing('feedback');
        foreach ($columns as $column) {
            echo "     - {$column}\n";
        }
    } else {
        echo "   ❌ Feedback table does not exist\n";
    }
    echo "\n";
    
    // Check users table
    echo "5. Users Table Analysis:\n";
    $totalUsers = User::count();
    echo "   Total users: {$totalUsers}\n";
    
    if ($totalUsers > 0) {
        echo "   Recent users:\n";
        $recentUsers = User::orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        foreach ($recentUsers as $user) {
            echo "     - ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, Admin: " . ($user->is_admin ? 'Yes' : 'No') . "\n";
        }
    }
    echo "\n";
    
    // Check database configuration
    echo "6. Database Configuration:\n";
    echo "   Host: " . config('database.connections.mysql.host') . "\n";
    echo "   Port: " . config('database.connections.mysql.port') . "\n";
    echo "   Database: " . config('database.connections.mysql.database') . "\n";
    echo "   Username: " . config('database.connections.mysql.username') . "\n";
    echo "\n";
    
    // Check migrations status
    echo "7. Migration Status:\n";
    $migrations = DB::table('migrations')->orderBy('batch', 'desc')->get();
    echo "   Total migrations run: " . $migrations->count() . "\n";
    
    if ($migrations->count() > 0) {
        echo "   Recent migrations:\n";
        foreach ($migrations->take(5) as $migration) {
            echo "     - {$migration->migration} (Batch: {$migration->batch})\n";
        }
    }
    echo "\n";
    
    // Summary and recommendations
    echo "8. Summary & Recommendations:\n";
    
    if ($totalBookings == 0 && $totalFeedback == 0) {
        echo "   🔍 ISSUE IDENTIFIED: Both bookings and feedback tables are empty\n";
        echo "   \n   Possible causes:\n";
        echo "   1. Forms are not submitting data correctly\n";
        echo "   2. Database connection issues during form submission\n";
        echo "   3. Validation errors preventing data insertion\n";
        echo "   4. Wrong database being used for phpMyAdmin vs application\n";
        echo "   \n   Next steps:\n";
        echo "   - Check form submission endpoints\n";
        echo "   - Verify database credentials match between app and phpMyAdmin\n";
        echo "   - Test creating a booking/feedback manually\n";
    } else if ($totalBookings > 0 || $totalFeedback > 0) {
        echo "   ✅ Data found in database - phpMyAdmin connection issue likely\n";
        echo "   \n   Recommendations:\n";
        echo "   - Verify phpMyAdmin is connecting to the same database\n";
        echo "   - Check phpMyAdmin configuration\n";
        echo "   - Try different phpMyAdmin URLs\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\nThis might indicate a database connection problem.\n";
    exit(1);
}