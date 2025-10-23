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
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('resource_type')->nullable()->after('user_id');
            $table->unsignedBigInteger('resource_id')->nullable()->after('resource_type');
            $table->json('old_values')->nullable()->after('resource_id');
            $table->json('new_values')->nullable()->after('old_values');
            $table->enum('severity_level', ['low', 'medium', 'high', 'critical'])->default('low')->after('new_values');
            $table->string('session_id')->nullable()->after('severity_level');
            
            // Add indexes for better performance
            $table->index(['resource_type', 'resource_id']);
            $table->index('severity_level');
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['resource_type', 'resource_id']);
            $table->dropIndex(['severity_level']);
            $table->dropIndex(['session_id']);
            
            $table->dropColumn([
                'resource_type',
                'resource_id',
                'old_values',
                'new_values',
                'severity_level',
                'session_id'
            ]);
        });
    }
};
