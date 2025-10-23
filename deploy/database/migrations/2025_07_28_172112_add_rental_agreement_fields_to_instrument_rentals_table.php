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
        Schema::table('instrument_rentals', function (Blueprint $table) {
            $table->boolean('id_provided')->default(false)->after('transportation');
            $table->string('venue_type')->default('indoor')->after('id_provided');
            $table->integer('event_duration_hours')->default(7)->after('venue_type');
            $table->boolean('documentation_consent')->default(true)->after('event_duration_hours');
            $table->decimal('reservation_fee', 8, 2)->default(300.00)->after('documentation_consent');
            $table->decimal('security_deposit', 8, 2)->default(300.00)->after('reservation_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instrument_rentals', function (Blueprint $table) {
            $table->dropColumn([
                'id_provided',
                'venue_type',
                'event_duration_hours',
                'documentation_consent',
                'reservation_fee',
                'security_deposit'
            ]);
        });
    }
};
