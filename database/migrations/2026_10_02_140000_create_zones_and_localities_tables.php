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
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('localities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained('zones')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->unique(['zone_id', 'name']);
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->foreignId('zone_id')->nullable()->after('short_introduction')->constrained('zones')->nullOnDelete();
            $table->foreignId('locality_id')->nullable()->after('zone_id')->constrained('localities')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropForeign(['zone_id']);
            $table->dropForeign(['locality_id']);
            $table->dropColumn(['zone_id', 'locality_id']);
        });

        Schema::dropIfExists('localities');
        Schema::dropIfExists('zones');
    }
};
