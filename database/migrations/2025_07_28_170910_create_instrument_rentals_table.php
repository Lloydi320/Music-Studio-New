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
        Schema::create('instrument_rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('instrument_type'); // guitar, amp, mic, keyboard, drums, etc.
            $table->string('instrument_name'); // specific instrument name/model
            $table->date('rental_start_date');
            $table->date('rental_end_date');
            $table->integer('rental_duration_days');
            $table->decimal('daily_rate', 8, 2);
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('pending'); // pending, confirmed, active, returned, cancelled
            $table->string('reference')->unique();
            $table->text('notes')->nullable();
            $table->string('pickup_location')->default('Studio');
            $table->string('return_location')->default('Studio');
            $table->string('transportation')->default('none');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instrument_rentals');
    }
};
