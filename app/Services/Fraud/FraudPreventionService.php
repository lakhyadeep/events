<?php

namespace App\Services\Fraud;

use App\Models\OtpSetting;
use Illuminate\Support\Facades\Cache;

class FraudPreventionService
{
    /**
     * Normalize and validate an Indian mobile number.
     * Accepts:
     *  - Standard 10-digit: 9876543210
     *  - Country code prefixed: +919876543210, 919876543210
     *  - Leading zero: 09876543210
     *
     * Returns a clean 10-digit string matching ^[6-9]\d{9}$, or null if invalid.
     */
    public function normalizeIndianMobile(string $phone): ?string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone) ?? '';

        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            $digits = substr($digits, 2);
        } elseif (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        if (strlen($digits) === 10 && preg_match('/^[6-9]\d{9}$/', $digits)) {
            return $digits;
        }

        return null;
    }

    /**
     * Validate an Indian mobile number format and check against disposable/virtual services.
     *
     * @return array{valid: bool, phone: ?string, message?: string}
     */
    public function validateMobileNumber(string $phone): array
    {
        $cleanPhone = $this->normalizeIndianMobile($phone);

        if (! $cleanPhone) {
            return [
                'valid' => false,
                'phone' => null,
                'message' => 'Please provide a valid 10-digit Indian mobile number.',
            ];
        }

        if ($this->isDisposableOrVirtual($cleanPhone)) {
            return [
                'valid' => false,
                'phone' => $cleanPhone,
                'message' => 'This mobile number is flagged as a virtual or temporary service and cannot be used for voting.',
            ];
        }

        return [
            'valid' => true,
            'phone' => $cleanPhone,
        ];
    }

    /**
     * Determine if a number belongs to known disposable/temporary SMS services or repeating dummy sequences.
     */
    public function isDisposableOrVirtual(string $cleanPhone): bool
    {
        // Whitelist test phones configured by administrators
        try {
            $setting = OtpSetting::current();
            if ($setting->isTestPhone($cleanPhone)) {
                return false;
            }
        } catch (\Throwable) {
            // Proceed with standard checks if settings unavailable
        }

        // Repeating single digits (e.g., 9999999999, 8888888888, 7777777777, 6666666666)
        if (preg_match('/^(\d)\1{9}$/', $cleanPhone)) {
            return true;
        }

        // Known disposable numbers list
        $disposableNumbers = (array) config('fraud.disposable_numbers', []);
        if (in_array($cleanPhone, $disposableNumbers, true)) {
            return true;
        }

        // Dynamic blacklist from Cache
        $cachedBlacklist = (array) Cache::get('fraud_disposable_blacklist', []);
        if (in_array($cleanPhone, $cachedBlacklist, true)) {
            return true;
        }

        return false;
    }

    /**
     * Check if the device / visitor ID has exceeded the maximum distinct phone numbers for an event.
     *
     * @return array{allowed: bool, message?: string}
     */
    public function checkDevicePhoneLimit(?string $visitorId, int $eventId, string $cleanPhone): array
    {
        if (empty($visitorId)) {
            return ['allowed' => true];
        }

        $maxPhones = (int) config('fraud.max_phones_per_device', 3);
        $cacheKey = "fraud_device_phones:{$eventId}:{$visitorId}";
        $usedPhones = (array) Cache::get($cacheKey, []);

        if (! in_array($cleanPhone, $usedPhones, true) && count($usedPhones) >= $maxPhones) {
            return [
                'allowed' => false,
                'message' => 'Maximum voting limit reached for this device. Multiple votes using different phone numbers on the same device are not permitted.',
            ];
        }

        return ['allowed' => true];
    }

    /**
     * Record a verified mobile number against a device visitor ID for an event.
     */
    public function recordDevicePhone(?string $visitorId, int $eventId, string $cleanPhone): void
    {
        if (empty($visitorId)) {
            return;
        }

        $cacheKey = "fraud_device_phones:{$eventId}:{$visitorId}";
        $usedPhones = (array) Cache::get($cacheKey, []);

        if (! in_array($cleanPhone, $usedPhones, true)) {
            $usedPhones[] = $cleanPhone;
            Cache::put($cacheKey, $usedPhones, now()->addDays(30));
        }
    }
}
