<?php

namespace App\Services\Otp;

use App\Models\Otp;
use App\Models\OtpSetting;
use App\Services\Otp\Data\OtpSendResult;
use App\Services\Otp\Data\OtpVerifyResult;
use Illuminate\Support\Facades\Log;

class OtpService
{
    public function __construct(
        protected OtpManager $otpManager
    ) {}

    /**
     * Generate and dispatch an OTP to the given phone number.
     */
    public function sendOtp(string $phone, string $purpose = 'verification', ?string $ip = null): OtpSendResult
    {
        $cleanPhone = $this->sanitizePhoneNumber($phone);

        if (strlen($cleanPhone) < 7 || strlen($cleanPhone) > 15) {
            return OtpSendResult::failure('Please provide a valid mobile number.');
        }

        $setting = OtpSetting::current();

        // 1. Check for active resend cooldown to protect against spam / flood attacks
        /** @var Otp|null $latestOtp */
        $latestOtp = Otp::query()
            ->where('phone', $cleanPhone)
            ->where('purpose', $purpose)
            ->latest('id')
            ->first();

        if ($latestOtp && $latestOtp->created_at) {
            $secondsSinceCreation = (int) now()->diffInSeconds($latestOtp->created_at);
            $cooldown = $setting->resend_cooldown_seconds;

            if ($secondsSinceCreation < $cooldown && ! $latestOtp->isVerified() && ! $latestOtp->isExpired()) {
                $wait = $cooldown - $secondsSinceCreation;

                return OtpSendResult::failure(
                    "Please wait {$wait} seconds before requesting a new OTP.",
                    resendAfterSeconds: $wait
                );
            }
        }

        // 2. Invalidate any prior active unverified OTPs for this phone + purpose
        Otp::query()
            ->where('phone', $cleanPhone)
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->update(['expires_at' => now()]);

        // 3. Determine OTP code
        $isTestNumber = $setting->isTestPhone($cleanPhone);
        if ($isTestNumber && filled($setting->master_otp)) {
            $code = (string) $setting->master_otp;
        } else {
            $code = $this->generateNumericCode($setting->otp_length);
        }

        $expiresAt = now()->addMinutes($setting->expiry_minutes);

        // 4. Record OTP with cryptographically secure hash
        $otpRecord = Otp::create([
            'phone' => $cleanPhone,
            'code_hash' => hash('sha256', $code),
            'purpose' => $purpose,
            'expires_at' => $expiresAt,
            'attempts' => 0,
            'ip_address' => $ip,
        ]);

        // 5. Build template message
        $template = $setting->message_template ?: 'Your verification code is :otp. Valid for :minutes minutes.';
        $message = str_replace(
            [':otp', ':minutes'],
            [$code, (string) $setting->expiry_minutes],
            $template
        );

        // 6. Dispatch through active gateway
        $gateway = $this->otpManager->driver($setting->active_driver);

        $smsResponse = $gateway->sendOtp($cleanPhone, $code, [
            'message' => $message,
            'purpose' => $purpose,
        ]);

        if (! $smsResponse->successful) {
            Log::error("Failed to send OTP to [{$cleanPhone}] via [{$gateway->getName()}]: {$smsResponse->error}");

            return OtpSendResult::failure(
                "Unable to deliver OTP via SMS: {$smsResponse->error}",
                resendAfterSeconds: 0,
                metadata: ['gateway' => $gateway->getName()]
            );
        }

        return OtpSendResult::success(
            message: 'OTP sent successfully to your mobile number.',
            resendAfterSeconds: $setting->resend_cooldown_seconds,
            expiresInSeconds: $setting->expiry_minutes * 60,
            gateway: $gateway->getName(),
            metadata: ['otp_id' => $otpRecord->id]
        );
    }

    /**
     * Verify a supplied OTP code for a phone number.
     */
    public function verifyOtp(string $phone, string $code, string $purpose = 'verification'): OtpVerifyResult
    {
        $cleanPhone = $this->sanitizePhoneNumber($phone);
        $cleanCode = trim($code);

        if ($cleanCode === '') {
            return OtpVerifyResult::failure('Please enter the OTP code.');
        }

        $setting = OtpSetting::current();

        // Check for test phone with master OTP bypass
        if ($setting->isTestPhone($cleanPhone) && filled($setting->master_otp) && $cleanCode === (string) $setting->master_otp) {
            // Mark any pending OTP record as verified
            Otp::query()
                ->where('phone', $cleanPhone)
                ->where('purpose', $purpose)
                ->whereNull('verified_at')
                ->latest('id')
                ->update(['verified_at' => now()]);

            return OtpVerifyResult::success('OTP verified successfully (Test Master Mode).');
        }

        /** @var Otp|null $otpRecord */
        $otpRecord = Otp::query()
            ->where('phone', $cleanPhone)
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->latest('id')
            ->first();

        if (! $otpRecord) {
            return OtpVerifyResult::failure('No active OTP found. Please request a new code.');
        }

        if ($otpRecord->isExpired()) {
            return OtpVerifyResult::failure('This OTP has expired. Please request a new one.');
        }

        if ($otpRecord->attempts >= $setting->max_attempts) {
            $otpRecord->update(['expires_at' => now()]);

            return OtpVerifyResult::failure(
                'Maximum verification attempts exceeded. Please request a new OTP.',
                attemptsRemaining: 0
            );
        }

        if (! $otpRecord->verifyCode($cleanCode)) {
            $otpRecord->increment('attempts');
            $remaining = max(0, $setting->max_attempts - $otpRecord->attempts);

            if ($remaining === 0) {
                $otpRecord->update(['expires_at' => now()]);

                return OtpVerifyResult::failure(
                    'Invalid OTP. Maximum attempts exceeded. Please request a new OTP.',
                    attemptsRemaining: 0
                );
            }

            return OtpVerifyResult::failure(
                "Invalid OTP code. You have {$remaining} attempt(s) remaining.",
                attemptsRemaining: $remaining
            );
        }

        // Successfully verified
        $otpRecord->update([
            'verified_at' => now(),
        ]);

        return OtpVerifyResult::success('OTP verified successfully.');
    }

    /**
     * Check if a phone has a recently verified OTP for a given purpose.
     */
    public function isVerified(string $phone, string $purpose = 'verification', int $validMinutes = 30): bool
    {
        $cleanPhone = $this->sanitizePhoneNumber($phone);

        return Otp::query()
            ->where('phone', $cleanPhone)
            ->where('purpose', $purpose)
            ->whereNotNull('verified_at')
            ->where('verified_at', '>=', now()->subMinutes($validMinutes))
            ->exists();
    }

    /**
     * Clean and normalize a mobile phone number to digits only.
     */
    public function sanitizePhoneNumber(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone) ?? '';
    }

    /**
     * Generate a cryptographically secure random numeric OTP.
     */
    protected function generateNumericCode(int $length = 6): string
    {
        $length = max(4, min(8, $length));
        $min = (int) ('1'.str_repeat('0', $length - 1));
        $max = (int) str_repeat('9', $length);

        return (string) random_int($min, $max);
    }
}
