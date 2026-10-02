<?php

namespace App\Models;

use App\Enums\SponsorType;
use App\Models\Concerns\ResolvesMediaUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sponsor extends Model
{
    use HasFactory;
    use ResolvesMediaUrl;

    protected $fillable = [
        'event_id',
        'title',
        'display_name',
        'sponsor_type',
        'sponsor_tag',
        'logo',
        'description',
        'landing_url',
        'slot_order',
        'year',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sponsor_type' => SponsorType::class,
            'is_active' => 'boolean',
            'slot_order' => 'integer',
            'year' => 'integer',
        ];
    }

    public function logoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->resolveMediaUrl($this->logo)
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

    public function scopePresenting(Builder $query): Builder
    {
        return $query->where('sponsor_type', SponsorType::PresentingPartner);
    }

    public function scopeAssociate(Builder $query): Builder
    {
        return $query->where('sponsor_type', SponsorType::AssociateSponsor)->orderBy('slot_order')->limit(5);
    }
}
