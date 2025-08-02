<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_webhook_channel_id')->nullable();
            $table->string('google_webhook_resource_id')->nullable();
            $table->timestamp('google_webhook_expiration')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_webhook_channel_id',
                'google_webhook_resource_id', 
                'google_webhook_expiration'
            ]);
        });
    }
};