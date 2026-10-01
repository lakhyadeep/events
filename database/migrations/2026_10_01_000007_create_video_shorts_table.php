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
        Schema::create('video_shorts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('name');
            $table->enum('platform', [
                'youtube_shorts',
                'youtube',
                'vimeo',
                'instagram',
            ])->default('youtube_shorts');
            $table->string('video_url');
            $table->string('video_id')->nullable();
            $table->text('caption')->nullable();
            $table->string('thumbnail_image')->nullable();
            $table->year('year')->index();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_shorts');
    }
};
