<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;

echo "=== Admin Users Database Status ===\n";
echo "📊 Current admin database information\n\n";

try {
    // Check if admin_users table exists
    $tableExists = DB::select("SHOW TABLES LIKE 'admin_users'");
    
    if (empty($tableExists)) {
        echo "❌ admin_users table does not exist\n";
        echo "💡 Run: php setup_admin_database.php\n";
        exit(1);
    }
    
    echo "✅ admin_users table exists\n\n";
    
    // Get admin statistics
    $totalAdmins = DB::table('admin_users')->count();
    $activeAdmins = DB::table('admin_users')->where('is_active', true)->count();
    $inactiveAdmins = DB::table('admin_users')->where('is_active', false)->count();
    $superAdmins = DB::table('admin_users')->where('role', 'super_admin')->count();
    $regularAdmins = DB::table('admin_users')->where('role', 'admin')->count();
    
    echo "📈 Database Statistics:\n";
    echo "  Total admin records: {$totalAdmins}\n";
    echo "  🟢 Active admins: {$activeAdmins}\n";
    echo "  🔴 Inactive admins: {$inactiveAdmins}\n";
    echo "  👑 Super admins: {$superAdmins}\n";
    echo "  👤 Regular admins: {$regularAdmins}\n\n";
    
    // Display all admin users
    echo "👥 Current Admin Users:\n";
    echo str_repeat('-', 80) . "\n";
    printf("%-3s %-20s %-30s %-12s %-8s %-15s\n", 'ID', 'Name', 'Email', 'Role', 'Status', 'Created By');
    echo str_repeat('-', 80) . "\n";
    
    $admins = DB::table('admin_users')
                ->select('id', 'name', 'email', 'role', 'is_active', 'created_by', 'created_at')
                ->orderBy('is_active', 'desc')
                ->orderBy('role', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
    
    foreach ($admins as $admin) {
        $status = $admin->is_active ? '🟢 Active' : '🔴 Inactive';
        $role = $admin->role === 'super_admin' ? '👑 Super' : '👤 Admin';
        
        printf("%-3d %-20s %-30s %-12s %-8s %-15s\n", 
            $admin->id,
            substr($admin->name, 0, 19),
            substr($admin->email, 0, 29),
            $role,
            $admin->is_active ? 'Active' : 'Inactive',
            substr($admin->created_by ?? 'Unknown', 0, 14)
        );
    }
    
    echo str_repeat('-', 80) . "\n\n";
    
    // Check view exists
    $viewExists = DB::select("SHOW TABLES LIKE 'admin_users_view'");
    if (!empty($viewExists)) {
        echo "✅ admin_users_view exists for easier phpMyAdmin browsing\n";
    } else {
        echo "⚠️  admin_users_view not found\n";
    }
    
    // Display phpMyAdmin access info
    echo "\n🌐 phpMyAdmin Access:\n";
    echo "  🔗 URL: http://localhost/phpmyadmin\n";
    echo "  🗄️  Database: " . config('database.connections.mysql.database') . "\n";
    echo "  📋 Table: admin_users\n";
    echo "  👁️  View: admin_users_view\n";
    
    // Display quick SQL queries
    echo "\n📝 Quick SQL Queries for phpMyAdmin:\n";
    echo "\n-- View all active admins\n";
    echo "SELECT * FROM admin_users_view WHERE status = 'Active';\n";
    
    echo "\n-- Add new admin\n";
    echo "INSERT INTO admin_users (name, email, role, permissions, is_active, created_by, notes, created_at, updated_at)\n";
    echo "VALUES ('New Admin', 'new@example.com', 'admin', '[\"manage_bookings\",\"view_dashboard\"]', 1, 'phpMyAdmin', 'Added manually', NOW(), NOW());\n";
    
    // Compare with main users table
    echo "\n🔄 Sync Status with Main Users Table:\n";
    $mainTableAdmins = User::where('is_admin', true)->count();
    $adminTableCount = DB::table('admin_users')->count();
    
    echo "  Main users table admins: {$mainTableAdmins}\n";
    echo "  Admin users table records: {$adminTableCount}\n";
    
    if ($mainTableAdmins === $adminTableCount) {
        echo "  ✅ Tables are in sync\n";
    } else {
        echo "  ⚠️  Tables may be out of sync\n";
        echo "  💡 Consider running sync operation\n";
    }
    
    echo "\n📖 For detailed management instructions, see: ADMIN_DATABASE_GUIDE.md\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== Status Check Complete ===\n";