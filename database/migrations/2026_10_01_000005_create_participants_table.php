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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('mobile_number', 20)->index();
            $table->string('name');
            $table->string('display_name');
            $table->text('short_introduction')->nullable();
            $table->string('zone')->index();
            $table->string('locality')->index();
            $table->text('address')->nullable();
            $table->string('landmark')->nullable();
            $table->string('key_contact_1_name')->nullable();
            $table->string('key_contact_1_phone', 20)->nullable();
            $table->string('key_contact_2_name')->nullable();
            $table->string('key_contact_2_phone', 20)->nullable();
            $table->string('primary_display_image');
            $table->string('image_1')->nullable();
            $table->string('image_2')->nullable();
            $table->string('image_3')->nullable();
            $table->year('year')->index();
            $table->enum('registration_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('is_shortlisted')->default(false)->index();

            // Cultural / Durga Puja contest specific attributes
            $table->boolean('is_puja_contest')->default(false);
            $table->year('first_year_of_puja')->nullable();
            $table->string('puja_theme')->nullable();
            $table->string('sound_designer')->nullable();
            $table->string('light_designer')->nullable();
            $table->string('idol_artist')->nullable();
            $table->string('theme_artist')->nullable();
            $table->string('concept_note_image')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
