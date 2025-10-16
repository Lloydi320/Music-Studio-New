<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'is_admin_walkin')) {
                $table->boolean('is_admin_walkin')->default(false)->after('status');
            }
            if (!Schema::hasColumn('bookings', 'created_by_admin_id')) {
                $table->unsignedBigInteger('created_by_admin_id')->nullable()->after('is_admin_walkin');
                $table->foreign('created_by_admin_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'created_by_admin_id')) {
                $table->dropForeign(['created_by_admin_id']);
                $table->dropColumn('created_by_admin_id');
            }
            if (Schema::hasColumn('bookings', 'is_admin_walkin')) {
                $table->dropColumn('is_admin_walkin');
            }
        });
    }
};