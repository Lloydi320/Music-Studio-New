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
        Schema::create('rehearsal_qr_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('duration_minutes')->unique();
            $table->decimal('reservation_fee_php', 8, 2);
            $table->string('qr_image_path'); // e.g. storage path like storage/qr/rehearsal/60.png
            $table->boolean('enabled')->default(true);
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_to')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rehearsal_qr_configs');
    }
};