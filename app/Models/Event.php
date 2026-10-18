<?php

namespace App\Models;

use App\Enums\BannerCategory;
use App\Enums\EventStatus;
use App\Enums\VotingMode;
use App\Models\Concerns\ResolvesMediaUrl;
use App\Services\Fraud\FraudPreventionService;
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

    public function getRouteKeyName(): string
    {
        return 'slug';
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

    public function heroBanners(): HasMany
    {
        return $this->banners()->where('category', BannerCategory::Hero);
    }

    public function marketingBanners(): HasMany
    {
        return $this->banners()->where('category', BannerCategory::Marketing);
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
     * Check if public voting is currently open for this event based on toggle and countdown deadline.
     */
    public function isVotingOpen(): bool
    {
        if (! $this->is_voting_active) {
            return false;
        }

        if ($this->countdown_datetime && now()->greaterThanOrEqualTo($this->countdown_datetime)) {
            return false;
        }

        return true;
    }

    /**
     * Get the descriptive closure message when voting is concluded or inactive.
     */
    public function getVotingClosedMessage(): string
    {
        if (filled($this->closure_message)) {
            return $this->closure_message;
        }

        if ($this->countdown_datetime && now()->greaterThanOrEqualTo($this->countdown_datetime)) {
            return 'Voting has officially concluded for this event.';
        }

        return 'Voting is currently closed for this event.';
    }

    /**
     * Check if a given mobile number is eligible to cast a vote under current event rules.
     *
     * @return array{allowed: bool, message: string}
     */
    public function canMobileVote(string $mobile, ?int $participantId = null, ?int $awardId = null): array
    {
        if (! $this->isVotingOpen()) {
            return [
                'allowed' => false,
                'message' => $this->getVotingClosedMessage(),
            ];
        }

        $fraudCheck = app(FraudPreventionService::class)->validateMobileNumber($mobile);
        if (! $fraudCheck['valid']) {
            return [
                'allowed' => false,
                'message' => $fraudCheck['message'] ?? 'Please provide a valid 10-digit Indian mobile number.',
            ];
        }

        $clean = $fraudCheck['phone'];

        // Mode 1: Participant Mode (Default)
        // One phone number can vote only one participant and one award category in the entire event.
        if ($this->voting_mode !== VotingMode::ParticipantAndAward) {
            $existingVote = $this->votes()
                ->where('mobile_number', $clean)
                ->with(['participant', 'award'])
                ->first();

            if ($existingVote) {
                $participant = $existingVote->participant;
                $award = $existingVote->award;

                $pName = $participant?->display_name ?: ($participant?->name ?: 'a candidate');
                $aName = $award?->display_name ?: ($award?->name ?: null);

                $details = $aName ? "\"{$pName}\" for \"{$aName}\"" : "\"{$pName}\"";

                return [
                    'allowed' => false,
                    'message' => "Mobile number +91 {$clean} has already cast a vote for {$details}. Only 1 vote per mobile number is permitted.",
                ];
            }

            return ['allowed' => true, 'message' => 'Eligible to vote.'];
        }

        // Mode 2: Participant and Award Category Mode
        // A mobile number can cast one vote in each award category.
        if ($awardId) {
            $alreadyVotedInAward = $this->votes()
                ->where('mobile_number', $clean)
                ->where('award_id', $awardId)
                ->exists();

            if ($alreadyVotedInAward) {
                $award = $this->awards()->find($awardId);
                $awardName = $award?->display_name ?: ($award?->name ?: 'this category');

                return [
                    'allowed' => false,
                    'message' => "Mobile number +91 {$clean} has already cast a vote for \"{$awardName}\". You can cast one vote in each award category.",
                ];
            }

            return ['allowed' => true, 'message' => 'Eligible to vote.'];
        }

        // General candidate voting (without award category) under ParticipantAndAward mode
        if ($participantId) {
            $alreadyVoted = $this->votes()
                ->where('mobile_number', $clean)
                ->where('participant_id', $participantId)
                ->whereNull('award_id')
                ->exists();

            if ($alreadyVoted) {
                $participant = $this->participants()->find($participantId);
                $participantName = $participant?->display_name ?: ($participant?->name ?: 'this candidate');

                return [
                    'allowed' => false,
                    'message' => "Mobile number +91 {$clean} has already cast a vote for \"{$participantName}\". You can vote for other participants, but only once per candidate.",
                ];
            }
        }

        return ['allowed' => true, 'message' => 'Eligible to vote.'];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}
