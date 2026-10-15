<?php

namespace App\Services\Otp\Drivers;

use App\Services\Otp\Contracts\SmsGatewayInterface;
use App\Services\Otp\Data\SmsResponse;
use Illuminate\Support\Facades\Log;

class LogSmsDriver implements SmsGatewayInterface
{
    /**
     * In-memory messages array for test verification.
     *
     * @var array<int, array{phone: string, message: string, options: array<string, mixed>}>
     */
    public static array $sentMessages = [];

    /**
     * Send an OTP code to a destination phone number.
     *
     * @param  array<string, mixed>  $options
     */
    public function sendOtp(string $phone, string $otp, array $options = []): SmsResponse
    {
        $message = "Your verification OTP is {$otp}.";

        return $this->sendSms($phone, $message, array_merge($options, ['otp' => $otp]));
    }

    /**
     * Send a general SMS message to a destination phone number.
     *
     * @param  array<string, mixed>  $options
     */
    public function sendSms(string $phone, string $message, array $options = []): SmsResponse
    {
        Log::info("SMS Sent via LogDriver to [{$phone}]: {$message}", $options);

        static::$sentMessages[] = [
            'phone' => $phone,
            'message' => $message,
            'options' => $options,
        ];

        return SmsResponse::success(
            messageId: 'log-'.uniqid(),
            rawPayload: ['phone' => $phone, 'message' => $message]
        );
    }

    /**
     * Clear all recorded messages in memory.
     */
    public static function clearSentMessages(): void
    {
        static::$sentMessages = [];
    }

    public function getName(): string
    {
        return 'log';
    }
}
