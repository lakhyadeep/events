<?php

namespace App\Models;

use App\Services\Fraud\FraudPreventionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'participant_id',
        'award_id',
        'mobile_number',
        'voter_name',
        'ip_address',
        'user_agent',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_id' => 'integer',
            'participant_id' => 'integer',
            'award_id' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }

    public function otps(): HasMany
    {
        return $this->hasMany(Otp::class, 'phone', 'mobile_number');
    }

    public function sameIpVotes(): HasMany
    {
        return $this->hasMany(Vote::class, 'ip_address', 'ip_address');
    }

    /**
     * Scope for a specific mobile number.
     *
     * @param  Builder<Vote>  $query
     * @return Builder<Vote>
     */
    public function scopeForMobile(Builder $query, string $mobile): Builder
    {
        $clean = preg_replace('/[^0-9]/', '', $mobile) ?? '';

        return $query->where('mobile_number', $clean);
    }

    /**
     * Integrity assessment for fraud detection.
     *
     * @return array{is_fraud: bool, label: string, color: string, reason: string}
     */
    public function getFraudAssessmentAttribute(): array
    {
        $rawPhone = (string) $this->mobile_number;
        $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone) ?? '';

        if (strlen($cleanPhone) === 12 && str_starts_with($cleanPhone, '91')) {
            $cleanPhone = substr($cleanPhone, 2);
        } elseif (strlen($cleanPhone) === 11 && str_starts_with($cleanPhone, '0')) {
            $cleanPhone = substr($cleanPhone, 1);
        }

        // 1. Format check
        if (strlen($cleanPhone) !== 10 || ! preg_match('/^[6-9]\d{9}$/', $cleanPhone)) {
            return [
                'is_fraud' => true,
                'label' => 'Invalid Mobile',
                'color' => 'danger',
                'reason' => 'Non-standard or foreign number format',
            ];
        }

        // 2. Blacklist / Disposable / Repeating check
        $fraudService = app(FraudPreventionService::class);
        if ($fraudService->isDisposableOrVirtual($cleanPhone)) {
            return [
                'is_fraud' => true,
                'label' => 'Disposable / Virtual',
                'color' => 'danger',
                'reason' => 'Flagged as temporary SMS or dummy number',
            ];
        }

        return [
            'is_fraud' => false,
            'label' => 'Verified Genuine',
            'color' => 'success',
            'reason' => 'Clean physical mobile number',
        ];
    }

    /**
     * Resolve a clean, human-readable device name from the recorded user agent.
     */
    public function getDeviceNameAttribute(): string
    {
        $userAgent = $this->user_agent;

        if (empty($userAgent)) {
            return 'Unknown';
        }

        if (preg_match('/iPhone/i', $userAgent)) {
            return 'iPhone';
        }

        if (preg_match('/iPad/i', $userAgent)) {
            return 'iPad';
        }

        if (preg_match('/iPod/i', $userAgent)) {
            return 'iPod';
        }

        if (stripos($userAgent, 'Android') !== false) {
            if (preg_match('/Android\s+[^;]+;\s*([^;\)]+?)(?:\s+Build|\)|;)/i', $userAgent, $matches)) {
                $model = trim($matches[1]);
                if (! empty($model) && strtoupper($model) !== 'K' && ! preg_match('/^[a-z]{2}(?:-[a-z]{2})?$/i', $model)) {
                    if (str_starts_with(strtoupper($model), 'SM-')) {
                        return 'Samsung '.$model;
                    }

                    return $model;
                }
            }

            return (stripos($userAgent, 'Mobile') !== false) ? 'Android Mobile' : 'Android Tablet';
        }

        if (stripos($userAgent, 'Windows') !== false) {
            return 'Windows PC';
        }

        if (stripos($userAgent, 'Macintosh') !== false || stripos($userAgent, 'Mac OS X') !== false) {
            return 'Mac';
        }

        if (stripos($userAgent, 'CrOS') !== false) {
            return 'Chromebook';
        }

        if (stripos($userAgent, 'Linux') !== false) {
            return 'Linux PC';
        }

        return 'Other';
    }

    /**
     * Count of total votes submitted from the same IP address.
     */
    public function getSameIpVotesCountAttribute(): int
    {
        if (empty($this->ip_address)) {
            return 0;
        }

        return static::where('ip_address', $this->ip_address)->count();
    }

    /**
     * Retrieve all votes submitted from the same IP address.
     *
     * @return Collection<int, Vote>
     */
    public function getSameIpVotesAttribute(): Collection
    {
        if (empty($this->ip_address)) {
            return new Collection([$this]);
        }

        return static::where('ip_address', $this->ip_address)
            ->with(['participant', 'award', 'event'])
            ->latest('id')
            ->get();
    }

    /**
     * Resolve comprehensive OTP audit trail data for this vote.
     *
     * @return array{
     *     has_otp: bool,
     *     status: string,
     *     status_color: string,
     *     dispatched_at: ?string,
     *     verified_at: ?string,
     *     verification_latency: ?string,
     *     attempts: int,
     *     otp_ip: ?string,
     *     ip_matches_vote: bool,
     *     total_dispatched: int,
     *     recent_otps: Collection<int, Otp>
     * }
     */
    public function getOtpAuditTrailAttribute(): array
    {
        /** @var Otp|null $otp */
        $otp = $this->otps()
            ->whereNotNull('verified_at')
            ->where('created_at', '<=', $this->created_at?->addMinutes(10) ?? now())
            ->latest('verified_at')
            ->first() ?? $this->otps()->latest('id')->first();

        $recentOtps = $this->otps()->latest('id')->take(5)->get();

        $status = 'No Record';
        $color = 'gray';

        if ($otp) {
            if ($otp->isVerified()) {
                $status = 'Verified';
                $color = 'success';
            } elseif ($otp->isExpired()) {
                $status = 'Expired';
                $color = 'danger';
            } else {
                $status = 'Pending / Unverified';
                $color = 'warning';
            }
        }

        $latency = null;
        if ($otp && $otp->created_at && $otp->verified_at) {
            $seconds = (int) $otp->created_at->diffInSeconds($otp->verified_at);
            if ($seconds < 60) {
                $latency = "{$seconds}s after dispatch";
            } else {
                $latency = (int) round($seconds / 60).'m after dispatch';
            }
        }

        return [
            'has_otp' => $otp !== null,
            'status' => $status,
            'status_color' => $color,
            'dispatched_at' => $otp?->created_at?->timezone('Asia/Kolkata')->format('d M Y, h:i:s A'),
            'verified_at' => $otp?->verified_at?->timezone('Asia/Kolkata')->format('d M Y, h:i:s A'),
            'verification_latency' => $latency,
            'attempts' => $otp?->attempts ?? 0,
            'otp_ip' => $otp?->ip_address,
            'ip_matches_vote' => $otp && $this->ip_address && $otp->ip_address === $this->ip_address,
            'total_dispatched' => $this->otps()->count(),
            'recent_otps' => $recentOtps,
        ];
    }
}
