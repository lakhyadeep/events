<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class OtpSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'active_driver',
        'otp_length',
        'expiry_minutes',
        'resend_cooldown_seconds',
        'max_attempts',
        'message_template',
        'master_otp',
        'test_phone_numbers',
        'twilio_settings',
        'msg91_settings',
        'fast2sms_settings',
        'custom_http_settings',
        'brevo_settings',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'otp_length' => 'integer',
            'expiry_minutes' => 'integer',
            'resend_cooldown_seconds' => 'integer',
            'max_attempts' => 'integer',
            'twilio_settings' => 'encrypted:array',
            'msg91_settings' => 'encrypted:array',
            'fast2sms_settings' => 'encrypted:array',
            'custom_http_settings' => 'encrypted:array',
            'brevo_settings' => 'encrypted:array',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }

    /**
     * Runtime static instance cache for request lifecycle.
     */
    protected static ?self $runtimeInstance = null;

    /**
     * Clear the cached OTP setting instance.
     */
    public static function clearCache(): void
    {
        static::$runtimeInstance = null;

        try {
            Cache::forget('otp_current_settings');
        } catch (\Throwable) {
            // Ignore cache forget issues
        }
    }

    /**
     * Retrieve the singleton settings record, using in-memory runtime cache.
     */
    public static function current(): self
    {
        if (static::$runtimeInstance instanceof self) {
            return static::$runtimeInstance;
        }

        try {
            $cached = Cache::get('otp_current_settings');
            if ($cached instanceof self) {
                return static::$runtimeInstance = $cached;
            }

            if ($cached !== null) {
                Cache::forget('otp_current_settings');
            }
        } catch (\Throwable) {
            // Safe fallback if serialized cache contains incompatible objects
            try {
                Cache::forget('otp_current_settings');
            } catch (\Throwable) {
            }
        }

        /** @var self $setting */
        $setting = static::query()->find(1);

        if (! $setting) {
            $setting = static::query()->create([
                'id' => 1,
                'active_driver' => config('otp.default', 'log'),
                'otp_length' => config('otp.length', 4),
                'expiry_minutes' => config('otp.expiry_minutes', 5),
                'resend_cooldown_seconds' => config('otp.resend_cooldown_seconds', 60),
                'max_attempts' => config('otp.max_attempts', 5),
                'message_template' => config('otp.message_template'),
                'master_otp' => config('otp.master_otp'),
                'test_phone_numbers' => implode(',', config('otp.test_phone_numbers', [])),
            ]);
        }

        static::$runtimeInstance = $setting;

        return $setting;
    }

    /**
     * Check if a given phone number is considered a designated test phone.
     */
    public function isTestPhone(string $phone): bool
    {
        if (blank($this->test_phone_numbers)) {
            return false;
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $phone) ?? '';
        if ($cleanPhone === '') {
            return false;
        }

        $testList = array_filter(array_map(
            fn (string $item) => preg_replace('/[^0-9]/', '', trim($item)) ?? '',
            explode(',', (string) $this->test_phone_numbers)
        ));

        foreach ($testList as $testItem) {
            if ($testItem === $cleanPhone) {
                return true;
            }

            // Match by 10-digit suffix to accommodate international country code prefixes (e.g. 91...)
            if (strlen($testItem) >= 10 && strlen($cleanPhone) >= 10) {
                if (substr($testItem, -10) === substr($cleanPhone, -10)) {
                    return true;
                }
            }
        }

        return false;
    }
}
