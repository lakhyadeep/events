<?php

namespace App\Services\Otp\Data;

readonly class SmsResponse
{
    /**
     * @param  array<string, mixed>  $rawPayload
     */
    public function __construct(
        public bool $successful,
        public ?string $messageId = null,
        public ?string $error = null,
        public array $rawPayload = [],
    ) {}

    /**
     * Create a successful response instance.
     *
     * @param  array<string, mixed>  $rawPayload
     */
    public static function success(?string $messageId = null, array $rawPayload = []): self
    {
        return new self(
            successful: true,
            messageId: $messageId,
            error: null,
            rawPayload: $rawPayload,
        );
    }

    /**
     * Create a failed response instance.
     *
     * @param  array<string, mixed>  $rawPayload
     */
    public static function failure(string $error, array $rawPayload = []): self
    {
        return new self(
            successful: false,
            messageId: null,
            error: $error,
            rawPayload: $rawPayload,
        );
    }
}
