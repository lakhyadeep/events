<?php

namespace App\Models;

use App\Enums\EventStatus;
use App\Models\Concerns\ResolvesMediaUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;
    use ResolvesMediaUrl;

    protected $fillable = [
        'name',
        'display_name',
        'year',
        'slug',
        'is_registration_active',
        'is_voting_active',
        'is_awards_active',
        'is_timeline_active',
        'tagline',
        'about_text',
        'criteria_text',
        'closure_message',
        'countdown_datetime',
        'terms_and_conditions',
        'og_image',
        'meta_title',
        'meta_description',
        'contact_email',
        'contact_phone',
        'external_link',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'is_registration_active' => 'boolean',
            'is_voting_active' => 'boolean',
            'is_awards_active' => 'boolean',
            'is_timeline_active' => 'boolean',
            'countdown_datetime' => 'datetime',
            'status' => EventStatus::class,
        ];
    }

    public function ogImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->resolveMediaUrl($this->og_image)
        );
    }

    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class)->orderBy('sort_order');
    }

    public function sponsors(): HasMany
    {
        return $this->hasMany(Sponsor::class)->orderBy('slot_order');
    }

    public function awards(): HasMany
    {
        return $this->hasMany(Award::class)->orderBy('sort_order');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function winners(): HasMany
    {
        return $this->hasMany(Winner::class);
    }

    public function videoShorts(): HasMany
    {
        return $this->hasMany(VideoShort::class)->orderBy('sort_order');
    }

    public function timelineItems(): HasMany
    {
        return $this->hasMany(TimelineItem::class)->orderBy('sort_order');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}
