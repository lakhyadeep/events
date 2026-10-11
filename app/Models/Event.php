<?php

namespace App\Models;

use App\Enums\EventStatus;
use App\Enums\VotingMode;
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
        'voting_mode',
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
            'voting_mode' => VotingMode::class,
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

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Check if a given mobile number is eligible to cast a vote under current event rules.
     *
     * @return array{allowed: bool, message: string}
     */
    public function canMobileVote(string $mobile, ?int $awardId = null): array
    {
        $clean = preg_replace('/[^0-9]/', '', $mobile) ?? '';
        if (strlen($clean) !== 10) {
            return ['allowed' => false, 'message' => 'Please provide a valid 10-digit mobile number.'];
        }

        if ($this->voting_mode === VotingMode::ParticipantAndAward) {
            if (! $awardId) {
                return ['allowed' => false, 'message' => 'Please select an award category for this vote.'];
            }

            $alreadyVoted = $this->votes()
                ->where('mobile_number', $clean)
                ->where('award_id', $awardId)
                ->exists();

            if ($alreadyVoted) {
                $award = $this->awards()->find($awardId);
                $awardName = $award?->display_name ?: ($award?->name ?: 'this category');

                return [
                    'allowed' => false,
                    'message' => "Mobile number +91 {$clean} has already cast a vote for \"{$awardName}\". Only 1 vote per award category is permitted.",
                ];
            }

            return ['allowed' => true, 'message' => 'Eligible to vote.'];
        }

        // Default 'participant' mode: 1 vote per mobile number across the entire event
        $alreadyVoted = $this->votes()
            ->where('mobile_number', $clean)
            ->exists();

        if ($alreadyVoted) {
            return [
                'allowed' => false,
                'message' => "Mobile number +91 {$clean} has already cast a vote in {$this->display_name}. Only 1 vote per phone number is permitted.",
            ];
        }

        return ['allowed' => true, 'message' => 'Eligible to vote.'];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}
