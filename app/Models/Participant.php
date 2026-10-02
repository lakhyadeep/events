<?php

namespace App\Models;

use App\Enums\RegistrationStatus;
use App\Models\Concerns\ResolvesMediaUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
{
    use HasFactory;
    use ResolvesMediaUrl;

    protected $fillable = [
        'event_id',
        'zone_id',
        'locality_id',
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
            'zone_id' => 'integer',
            'locality_id' => 'integer',
            'is_shortlisted' => 'boolean',
            'is_puja_contest' => 'boolean',
            'year' => 'integer',
            'first_year_of_puja' => 'integer',
            'registration_status' => RegistrationStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Participant $participant) {
            // Keep legacy zone & locality text columns in sync if relation IDs are set
            if ($participant->zone_id && empty($participant->zone)) {
                $participant->zone = Zone::find($participant->zone_id)?->name ?? $participant->zone;
            }
            if ($participant->locality_id && empty($participant->locality)) {
                $participant->locality = Locality::find($participant->locality_id)?->name ?? $participant->locality;
            }
        });
    }

    public function primaryImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->resolveMediaUrl($this->primary_display_image)
        );
    }

    public function image1Url(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->resolveMediaUrl($this->image_1)
        );
    }

    public function image2Url(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->resolveMediaUrl($this->image_2)
        );
    }

    public function image3Url(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->resolveMediaUrl($this->image_3)
        );
    }

    public function conceptNoteImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->resolveMediaUrl($this->concept_note_image)
        );
    }

    public function galleryImageUrls(): Attribute
    {
        return Attribute::make(
            get: function () {
                $images = [];
                if ($this->primary_image_url) {
                    $images[] = $this->primary_image_url;
                }
                foreach ([$this->image_1_url, $this->image_2_url, $this->image_3_url] as $img) {
                    if ($img) {
                        $images[] = $img;
                    }
                }

                return $images;
            }
        );
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function locality(): BelongsTo
    {
        return $this->belongsTo(Locality::class, 'locality_id');
    }

    public function winners(): HasMany
    {
        return $this->hasMany(Winner::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('registration_status', RegistrationStatus::Approved);
    }

    public function scopeShortlisted(Builder $query): Builder
    {
        return $query->where('is_shortlisted', true);
    }

    public function scopeZone(Builder $query, Zone|string|int|null $zone): Builder
    {
        if (blank($zone)) {
            return $query;
        }

        if ($zone instanceof Zone) {
            return $query->where('zone_id', $zone->id);
        }

        if (is_numeric($zone)) {
            return $query->where('zone_id', (int) $zone);
        }

        return $query->where(function ($q) use ($zone) {
            $q->where('zone', $zone)
                ->orWhereHas('zone', fn ($sub) => $sub->where('name', $zone)->orWhere('slug', $zone));
        });
    }
}
