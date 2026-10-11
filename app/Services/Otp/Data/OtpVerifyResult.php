<?php

namespace App\Services\Otp\Data;

readonly class OtpVerifyResult
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public bool $successful,
        public string $message,
        public ?int $attemptsRemaining = null,
        public array $metadata = [],
    ) {}

    public static function success(string $message = 'OTP verified successfully.', array $metadata = []): self
    {
        return new self(
            successful: true,
            message: $message,
            attemptsRemaining: null,
            metadata: $metadata,
        );
    }

    public static function failure(string $message, ?int $attemptsRemaining = null, array $metadata = []): self
    {
        return new self(
            successful: false,
            message: $message,
            attemptsRemaining: $attemptsRemaining,
            metadata: $metadata,
        );
    }
}
