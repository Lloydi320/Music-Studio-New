<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure new column exists
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'is_walk_in_booking')) {
                $table->boolean('is_walk_in_booking')->default(false)->after('status');
            }
        });

        // Copy data from old column if present, then drop it
        if (Schema::hasColumn('bookings', 'is_admin_walkin')) {
            // Copy values from old to new
            try {
                DB::statement('UPDATE bookings SET is_walk_in_booking = is_admin_walkin');
            } catch (\Throwable $e) {
                // If statement fails (e.g., different driver), fallback by updating in chunks
                $rows = DB::table('bookings')->select('id', 'is_admin_walkin')->get();
                foreach ($rows as $row) {
                    DB::table('bookings')->where('id', $row->id)->update([
                        'is_walk_in_booking' => (bool) ($row->is_admin_walkin ?? false),
                    ]);
                }
            }

            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn('is_admin_walkin');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore old column
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'is_admin_walkin')) {
                $table->boolean('is_admin_walkin')->default(false)->after('status');
            }
        });

        // Copy values back and drop new column
        if (Schema::hasColumn('bookings', 'is_walk_in_booking')) {
            try {
                DB::statement('UPDATE bookings SET is_admin_walkin = is_walk_in_booking');
            } catch (\Throwable $e) {
                $rows = DB::table('bookings')->select('id', 'is_walk_in_booking')->get();
                foreach ($rows as $row) {
                    DB::table('bookings')->where('id', $row->id)->update([
                        'is_admin_walkin' => (bool) ($row->is_walk_in_booking ?? false),
                    ]);
                }
            }

            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn('is_walk_in_booking');
            });
        }
    }
};