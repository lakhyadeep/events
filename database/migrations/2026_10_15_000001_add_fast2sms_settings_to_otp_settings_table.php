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
        if (Schema::hasTable('otp_settings') && ! Schema::hasColumn('otp_settings', 'fast2sms_settings')) {
            Schema::table('otp_settings', function (Blueprint $table) {
                $table->text('fast2sms_settings')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('otp_settings') && Schema::hasColumn('otp_settings', 'fast2sms_settings')) {
            Schema::table('otp_settings', function (Blueprint $table) {
                $table->dropColumn('fast2sms_settings');
            });
        }
    }
};
