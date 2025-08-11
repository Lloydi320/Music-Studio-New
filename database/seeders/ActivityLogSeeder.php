<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ActivityLog;
use Carbon\Carbon;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sampleLogs = [
            [
                'user_name' => 'Admin User',
                'user_role' => 'Admin',
                'description' => 'Admin logged in',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'action_type' => 'login',
                'user_id' => 1,
                'created_at' => Carbon::now()->subMinutes(5),
                'updated_at' => Carbon::now()->subMinutes(5),
            ],
            [
                'user_name' => 'Admin User',
                'user_role' => 'Admin',
                'description' => 'User logged out',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'action_type' => 'logout',
                'user_id' => 1,
                'created_at' => Carbon::now()->subMinutes(3),
                'updated_at' => Carbon::now()->subMinutes(3),
            ],
            [
                'user_name' => 'Admin User',
                'user_role' => 'Admin',
                'description' => 'Admin logged in',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'action_type' => 'login',
                'user_id' => 1,
                'created_at' => Carbon::now()->subMinutes(2),
                'updated_at' => Carbon::now()->subMinutes(2),
            ],
            [
                'user_name' => 'Luka Doncic',
                'user_role' => 'Customer',
                'description' => 'Created chatbot booking #BPR-C17532FA for (extended) from 2025-11-29 to 2025-11-30 (₱10000)',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'action_type' => 'booking_created',
                'user_id' => 2,
                'created_at' => Carbon::now()->subMinutes(7),
                'updated_at' => Carbon::now()->subMinutes(7),
            ],
            [
                'user_name' => 'Luka Doncic',
                'user_role' => 'Customer',
                'description' => 'Booking #BPR-A434EDBB cancelled - Check-in: 2025-11-28, Refund: full',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'action_type' => 'booking_cancelled',
                'user_id' => 2,
                'created_at' => Carbon::now()->subMinutes(11),
                'updated_at' => Carbon::now()->subMinutes(11),
            ],
            [
                'user_name' => 'Luka Doncic',
                'user_role' => 'Customer',
                'description' => 'Created chatbot booking #BPR-A434EDBB for (extended) from 2025-11-29 to 2025-11-30 (₱10000)',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'action_type' => 'booking_created',
                'user_id' => 2,
                'created_at' => Carbon::now()->subMinutes(8),
                'updated_at' => Carbon::now()->subMinutes(8),
            ],
            [
                'user_name' => 'Luka Doncic',
                'user_role' => 'Customer',
                'description' => 'User logged in successfully',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'action_type' => 'login',
                'user_id' => 2,
                'created_at' => Carbon::now()->subMinutes(1),
                'updated_at' => Carbon::now()->subMinutes(1),
            ],
        ];

        foreach ($sampleLogs as $log) {
            ActivityLog::create($log);
        }
    }
}
