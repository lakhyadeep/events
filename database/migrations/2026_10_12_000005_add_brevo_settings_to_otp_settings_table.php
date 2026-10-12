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
        Schema::table('otp_settings', function (Blueprint $table) {
            $table->text('brevo_settings')->nullable()->after('custom_http_settings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('otp_settings', function (Blueprint $table) {
            $table->dropColumn('brevo_settings');
        });
    }
};
