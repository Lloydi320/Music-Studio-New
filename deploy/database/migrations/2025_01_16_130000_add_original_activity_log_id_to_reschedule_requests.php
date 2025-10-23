<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reschedule_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('original_activity_log_id')->nullable()->after('id');
            $table->index('original_activity_log_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reschedule_requests', function (Blueprint $table) {
            $table->dropIndex(['original_activity_log_id']);
            $table->dropColumn('original_activity_log_id');
        });
    }
};