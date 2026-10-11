<?php

namespace App\Services\Otp\Contracts;

use App\Services\Otp\Data\SmsResponse;

interface SmsGatewayInterface
{
    /**
     * Send an OTP code to a destination phone number.
     *
     * @param  array<string, mixed>  $options
     */
    public function sendOtp(string $phone, string $otp, array $options = []): SmsResponse;

    /**
     * Send a general SMS message to a destination phone number.
     *
     * @param  array<string, mixed>  $options
     */
    public function sendSms(string $phone, string $message, array $options = []): SmsResponse;

    /**
     * Get the identifier name of the gateway driver.
     */
    public function getName(): string;
}
