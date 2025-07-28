<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\InstrumentRental;

try {
    echo "=== MySQL Database Connection Verification ===\n\n";
    
    // Test database connection
    $connection = DB::connection();
    $databaseName = $connection->getDatabaseName();
    echo "✅ Successfully connected to MySQL database: {$databaseName}\n\n";
    
    // Show database configuration
    echo "Database Configuration:\n";
    echo "- Host: " . config('database.connections.mysql.host') . "\n";
    echo "- Port: " . config('database.connections.mysql.port') . "\n";
    echo "- Database: " . config('database.connections.mysql.database') . "\n";
    echo "- Username: " . config('database.connections.mysql.username') . "\n\n";
    
    // List all tables
    echo "Available Tables:\n";
    $tables = DB::select('SHOW TABLES');
    foreach ($tables as $table) {
        $tableName = array_values((array) $table)[0];
        echo "- {$tableName}\n";
    }
    echo "\n";
    
    // Show instrument_rentals table structure
    echo "Instrument Rentals Table Structure:\n";
    $columns = DB::select('DESCRIBE instrument_rentals');
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    echo "\n";
    
    // Count records in instrument_rentals
    $count = InstrumentRental::count();
    echo "Total Instrument Rental Records: {$count}\n\n";
    
    echo "✅ Instrument rental system is successfully connected to phpMyAdmin-accessible MySQL database!\n";
    echo "\nYou can now access the database through phpMyAdmin at:\n";
    echo "- URL: http://localhost/phpmyadmin\n";
    echo "- Database: music_studio_new\n";
    echo "- Table: instrument_rentals\n";
    
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}