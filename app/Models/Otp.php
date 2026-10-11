<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'code_hash',
        'purpose',
        'expires_at',
        'verified_at',
        'attempts',
        'ip_address',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    /**
     * Scope for valid unverified, non-expired OTPs.
     *
     * @param  Builder<Otp>  $query
     * @return Builder<Otp>
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query->whereNull('verified_at')
            ->where('expires_at', '>', now());
    }

    /**
     * Check if the OTP is currently expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the OTP has already been verified.
     */
    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    /**
     * Verify whether a raw candidate code matches this OTP's hash.
     */
    public function verifyCode(string $code): bool
    {
        $expectedHash = hash('sha256', (string) $code);

        return hash_equals($this->code_hash, $expectedHash);
    }
}
