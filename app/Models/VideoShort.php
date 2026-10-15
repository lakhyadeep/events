<?php

namespace App\Models;

use App\Enums\VideoPlatform;
use App\Models\Concerns\ResolvesMediaUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoShort extends Model
{
    use HasFactory;
    use ResolvesMediaUrl;

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
            'platform' => VideoPlatform::class,
            'is_active' => 'boolean',
            'year' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function thumbnailImageUrl(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                if ($resolved = $this->resolveMediaUrl($this->thumbnail_image)) {
                    return $resolved;
                }

                $id = $this->video_id;
                if (! $id && ! empty($this->video_url)) {
                    if (preg_match('/(?:shorts\/|youtu\.be\/|v=|\/embed\/|\/v\/)([\w\-]{11})/', $this->video_url, $matches)) {
                        $id = $matches[1];
                    }
                }

                if ($id) {
                    return "https://img.youtube.com/vi/{$id}/hqdefault.jpg";
                }

                return null;
            }
        );
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
        $id = $this->video_id;
        if (! $id && ! empty($this->video_url)) {
            if (preg_match('/(?:shorts\/|youtu\.be\/|v=|\/embed\/|\/v\/)([\w\-]{11})/', $this->video_url, $matches)) {
                $id = $matches[1];
            } elseif (preg_match('/(?:shorts\/|youtu\.be\/|v=)([\w\-]+)/', $this->video_url, $matches)) {
                $id = $matches[1];
            }
        }

        if ($id) {
            return "https://www.youtube-nocookie.com/embed/{$id}?autoplay=1&enablejsapi=1&rel=0&playsinline=1&modestbranding=1&loop=1&playlist={$id}";
        }

        return $this->video_url ?? '';
    }
}
