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
        Schema::create('rental_qr_configs', function (Blueprint $table) {
            $table->id();
            $table->string('rental_type')->unique(); // instruments | full_package
            $table->decimal('reservation_fee_php', 8, 2);
            $table->string('qr_image_path'); // storage path like storage/qr/rental/instruments.png
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_qr_configs');
    }
};