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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_name');
            $table->string('user_role');
            $table->text('description');
            $table->ipAddress('ip_address');
            $table->string('user_agent')->nullable();
            $table->string('action_type')->nullable(); // login, logout, booking_created, etc.
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            
            $table->index(['created_at', 'user_role']);
            $table->index('action_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
