<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'mobile_number',
        'name',
        'display_name',
        'short_introduction',
        'zone',
        'locality',
        'address',
        'landmark',
        'key_contact_1_name',
        'key_contact_1_phone',
        'key_contact_2_name',
        'key_contact_2_phone',
        'primary_display_image',
        'image_1',
        'image_2',
        'image_3',
        'year',
        'registration_status',
        'is_shortlisted',
        'is_puja_contest',
        'first_year_of_puja',
        'puja_theme',
        'sound_designer',
        'light_designer',
        'idol_artist',
        'theme_artist',
        'concept_note_image',
    ];

    protected function casts(): array
    {
        return [
            'is_shortlisted' => 'boolean',
            'is_puja_contest' => 'boolean',
            'year' => 'integer',
            'first_year_of_puja' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function winners(): HasMany
    {
        return $this->hasMany(Winner::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('registration_status', 'approved');
    }

    public function scopeShortlisted(Builder $query): Builder
    {
        return $query->where('is_shortlisted', true);
    }

    public function scopeZone(Builder $query, ?string $zone): Builder
    {
        return $zone ? $query->where('zone', $zone) : $query;
    }
}
