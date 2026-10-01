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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('display_name');
            $table->year('year')->index();
            $table->string('slug')->unique();
            $table->boolean('is_registration_active')->default(false);
            $table->boolean('is_voting_active')->default(false);
            $table->boolean('is_awards_active')->default(false);
            $table->boolean('is_timeline_active')->default(true);
            $table->string('tagline')->nullable();
            $table->longText('about_text')->nullable();
            $table->longText('criteria_text')->nullable();
            $table->text('closure_message')->nullable();
            $table->dateTime('countdown_datetime')->nullable();
            $table->longText('terms_and_conditions')->nullable();
            $table->string('og_image')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('external_link')->nullable();
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
