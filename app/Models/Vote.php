<?php

namespace App\Models;

use App\Services\Fraud\FraudPreventionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
