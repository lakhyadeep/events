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
        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('title');
            $table->string('display_name');
            $table->enum('sponsor_type', [
                'presenting_partner',
                'associate_sponsor',
                'powered_by',
                'co_sponsor',
                'partner',
            ])->index();
            $table->string('sponsor_tag')->nullable();
            $table->string('logo');
            $table->text('description')->nullable();
            $table->string('landing_url')->nullable();
            $table->unsignedTinyInteger('slot_order')->default(1);
            $table->year('year')->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsors');
    }
};
