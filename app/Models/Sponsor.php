<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sponsor extends Model
{
    use HasFactory;

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
            'is_active' => 'boolean',
            'slot_order' => 'integer',
            'year' => 'integer',
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

    public function scopePresenting(Builder $query): Builder
    {
        return $query->where('sponsor_type', 'presenting_partner');
    }

    public function scopeAssociate(Builder $query): Builder
    {
        return $query->where('sponsor_type', 'associate_sponsor')->orderBy('slot_order')->limit(5);
    }
}
