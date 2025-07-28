<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class AdminUsersSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // First, sync existing admin users from the users table
        $existingAdmins = User::where('is_admin', true)->get();
        
        foreach ($existingAdmins as $admin) {
            DB::table('admin_users')->updateOrInsert(
                ['email' => $admin->email],
                [
                    'name' => $admin->name,
                    'email' => $admin->email,
                    'role' => 'admin',
                    'permissions' => json_encode([
                        'manage_bookings',
                        'manage_users',
                        'view_dashboard',
                        'manage_calendar',
                        'database_access'
                    ]),
                    'is_active' => true,
                    'created_by' => 'system_migration',
                    'notes' => 'Migrated from existing admin user',
                    'created_at' => $admin->created_at ?? Carbon::now(),
                    'updated_at' => Carbon::now()
                ]
            );
        }
        
        // Create additional sample admin users for demonstration
        $sampleAdmins = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@musicstudio.com',
                'role' => 'super_admin',
                'permissions' => json_encode([
                    'manage_bookings',
                    'manage_users',
                    'view_dashboard',
                    'manage_calendar',
                    'database_access',
                    'system_settings',
                    'user_management',
                    'backup_restore'
                ]),
                'is_active' => true,
                'created_by' => 'system',
                'notes' => 'System super administrator with full access'
            ],
            [
                'name' => 'Calendar Admin',
                'email' => 'calendar@musicstudio.com',
                'role' => 'admin',
                'permissions' => json_encode([
                    'manage_bookings',
                    'view_dashboard',
                    'manage_calendar'
                ]),
                'is_active' => true,
                'created_by' => 'system',
                'notes' => 'Specialized admin for calendar and booking management'
            ],
            [
                'name' => 'Support Admin',
                'email' => 'support@musicstudio.com',
                'role' => 'admin',
                'permissions' => json_encode([
                    'manage_bookings',
                    'manage_users',
                    'view_dashboard'
                ]),
                'is_active' => false,
                'created_by' => 'system',
                'notes' => 'Support staff admin (currently inactive)'
            ]
        ];
        
        foreach ($sampleAdmins as $admin) {
            DB::table('admin_users')->updateOrInsert(
                ['email' => $admin['email']],
                array_merge($admin, [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ])
            );
        }
        
        echo "\n✅ Admin users table seeded successfully!\n";
        echo "📊 Total admin records: " . DB::table('admin_users')->count() . "\n";
        echo "🔴 Active admins: " . DB::table('admin_users')->where('is_active', true)->count() . "\n";
        echo "⚪ Inactive admins: " . DB::table('admin_users')->where('is_active', false)->count() . "\n";
    }
}