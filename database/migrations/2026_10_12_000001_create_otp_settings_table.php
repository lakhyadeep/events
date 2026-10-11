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
        Schema::create('otp_settings', function (Blueprint $table) {
            $table->id();
            $table->string('active_driver')->default('log');
            $table->unsignedSmallInteger('otp_length')->default(6);
            $table->unsignedSmallInteger('expiry_minutes')->default(5);
            $table->unsignedSmallInteger('resend_cooldown_seconds')->default(60);
            $table->unsignedSmallInteger('max_attempts')->default(5);
            $table->text('message_template')->nullable();
            $table->string('master_otp')->nullable();
            $table->text('test_phone_numbers')->nullable();
            $table->text('twilio_settings')->nullable();
            $table->text('msg91_settings')->nullable();
            $table->text('fast2sms_settings')->nullable();
            $table->text('custom_http_settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_settings');
    }
};
