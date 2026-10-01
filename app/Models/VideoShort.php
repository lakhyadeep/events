<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoShort extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'platform',
        'video_url',
        'video_id',
        'caption',
        'thumbnail_image',
        'year',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'year' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Helper to extract embed URL or YouTube video ID.
     */
    public function getEmbedUrlAttribute(): string
    {
        if ($this->video_id) {
            return "https://www.youtube.com/embed/{$this->video_id}?autoplay=1";
        }

        // Parse youtube shorts URL if video_id is not pre-populated
        if (preg_match('/(?:shorts\/|youtu\.be\/|v=)([\w\-]+)/', $this->video_url, $matches)) {
            return "https://www.youtube.com/embed/{$matches[1]}?autoplay=1";
        }

        return $this->video_url;
    }
}
