<?php

namespace App\Services\Otp\Data;

readonly class OtpSendResult
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public bool $successful,
        public string $message,
        public ?int $resendAfterSeconds = null,
        public ?int $expiresInSeconds = null,
        public ?string $gateway = null,
        public array $metadata = [],
    ) {}

    public static function success(
        string $message,
        int $resendAfterSeconds = 60,
        int $expiresInSeconds = 300,
        ?string $gateway = null,
        array $metadata = []
    ): self {
        return new self(
            successful: true,
            message: $message,
            resendAfterSeconds: $resendAfterSeconds,
            expiresInSeconds: $expiresInSeconds,
            gateway: $gateway,
            metadata: $metadata,
        );
    }

    public static function failure(string $message, ?int $resendAfterSeconds = null, array $metadata = []): self
    {
        return new self(
            successful: false,
            message: $message,
            resendAfterSeconds: $resendAfterSeconds,
            expiresInSeconds: null,
            gateway: null,
            metadata: $metadata,
        );
    }
}
