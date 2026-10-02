<?php

namespace App\Models;

use App\Models\Concerns\ResolvesMediaUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Banner extends Model
{
    use HasFactory;
    use ResolvesMediaUrl;

    protected $fillable = [
        'event_id',
        'title',
        'image_desktop',
        'image_mobile',
        'cta_link',
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

    public function desktopImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->resolveMediaUrl($this->image_desktop)
        );
    }

    public function mobileImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->resolveMediaUrl($this->image_mobile)
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
}
