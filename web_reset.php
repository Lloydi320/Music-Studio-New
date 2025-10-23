<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Reset - Music Studio</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .btn {
            background: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .log {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            white-space: pre-wrap;
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎵 Music Studio Database Reset</h1>
        
        <?php
        // Include Laravel bootstrap
        if (file_exists(__DIR__ . '/vendor/autoload.php')) {
            require_once __DIR__ . '/vendor/autoload.php';
            $app = require_once __DIR__ . '/bootstrap/app.php';
            $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
            $kernel->bootstrap();
            
            use Illuminate\Support\Facades\DB;
            use Illuminate\Support\Facades\Schema;
            
            if (isset($_POST['confirm_reset'])) {
                echo '<div class="log">';
                echo "=== STARTING DATABASE RESET ===\n";
                
                try {
                    DB::beginTransaction();
                    
                    // Get current counts
                    $userCount = DB::table('users')->count();
                    $bookingCount = Schema::hasTable('bookings') ? DB::table('bookings')->count() : 0;
                    $feedbackCount = Schema::hasTable('feedback') ? DB::table('feedback')->count() : 0;
                    
                    echo "Current database state:\n";
                    echo "- Users: {$userCount}\n";
                    echo "- Bookings: {$bookingCount}\n";
                    echo "- Feedback: {$feedbackCount}\n\n";
                    
                    // Clear bookings
                    if (Schema::hasTable('bookings') && $bookingCount > 0) {
                        DB::table('bookings')->truncate();
                        echo "✓ Cleared {$bookingCount} booking records\n";
                    }
                    
                    // Clear feedback
                    if (Schema::hasTable('feedback') && $feedbackCount > 0) {
                        DB::table('feedback')->truncate();
                        echo "✓ Cleared {$feedbackCount} feedback records\n";
                    }
                    
                    // Clear rental tables
                    $rentalTables = ['rentals', 'instrument_rentals', 'equipment_rentals'];
                    foreach ($rentalTables as $table) {
                        if (Schema::hasTable($table)) {
                            $count = DB::table($table)->count();
                            if ($count > 0) {
                                DB::table($table)->truncate();
                                echo "✓ Cleared {$count} records from {$table}\n";
                            }
                        }
                    }
                    
                    // Reset auto-increment
                    if (Schema::hasTable('bookings')) {
                        DB::statement('ALTER TABLE bookings AUTO_INCREMENT = 1');
                        echo "✓ Reset bookings auto-increment\n";
                    }
                    
                    if (Schema::hasTable('feedback')) {
                        DB::statement('ALTER TABLE feedback AUTO_INCREMENT = 1');
                        echo "✓ Reset feedback auto-increment\n";
                    }
                    
                    // Verify users are intact
                    $finalUserCount = DB::table('users')->count();
                    echo "\nVerification:\n";
                    echo "Users before: {$userCount}\n";
                    echo "Users after: {$finalUserCount}\n";
                    
                    if ($userCount === $finalUserCount) {
                        DB::commit();
                        echo "\n✅ RESET COMPLETED SUCCESSFULLY!\n";
                        echo "- All booking data cleared\n";
                        echo "- User accounts preserved\n";
                        echo "- Ready for fresh bookings\n";
                        
                        // Clear Laravel cache
                        try {
                            \Illuminate\Support\Facades\Artisan::call('cache:clear');
                            \Illuminate\Support\Facades\Artisan::call('config:clear');
                            \Illuminate\Support\Facades\Artisan::call('view:clear');
                            echo "✓ Laravel cache cleared\n";
                        } catch (Exception $e) {
                            echo "⚠ Cache clear failed: " . $e->getMessage() . "\n";
                        }
                        
                    } else {
                        throw new Exception("User count mismatch! Rolling back.");
                    }
                    
                } catch (Exception $e) {
                    DB::rollBack();
                    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
                    echo "All changes rolled back.\n";
                }
                
                echo "\n=== RESET PROCESS COMPLETED ===";
                echo '</div>';
                
                echo '<p><a href="?" class="btn">Reset Another Time</a></p>';
                
            } else {
                // Show confirmation form
                $userCount = DB::table('users')->count();
                $bookingCount = Schema::hasTable('bookings') ? DB::table('bookings')->count() : 0;
                $feedbackCount = Schema::hasTable('feedback') ? DB::table('feedback')->count() : 0;
                
                echo '<div class="warning">';
                echo '<strong>⚠️ WARNING:</strong> This will permanently delete all booking and rental data!<br>';
                echo 'User accounts will be preserved.';
                echo '</div>';
                
                echo '<h3>Current Database Status:</h3>';
                echo '<ul>';
                echo "<li><strong>Users:</strong> {$userCount} (will be preserved)</li>";
                echo "<li><strong>Bookings:</strong> {$bookingCount} (will be deleted)</li>";
                echo "<li><strong>Feedback:</strong> {$feedbackCount} (will be deleted)</li>";
                echo '</ul>';
                
                echo '<form method="post">';
                echo '<button type="submit" name="confirm_reset" class="btn btn-danger">🗑️ CONFIRM RESET DATABASE</button>';
                echo '<a href="/" class="btn">Cancel</a>';
                echo '</form>';
            }
            
        } else {
            echo '<div class="error">Laravel framework not found. Please ensure this file is in the Laravel root directory.</div>';
        }
        ?>
        
        <hr style="margin: 30px 0;">
        <p><small>Music Studio Management System - Database Reset Tool</small></p>
    </div>
</body>
</html>