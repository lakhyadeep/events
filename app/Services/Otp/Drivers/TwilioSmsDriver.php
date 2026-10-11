<?php

namespace App\Services\Otp\Drivers;

use App\Services\Otp\Contracts\SmsGatewayInterface;
use App\Services\Otp\Data\SmsResponse;
use Illuminate\Support\Facades\Http;
use Throwable;

class TwilioSmsDriver implements SmsGatewayInterface
{
    /**
     * @param  array{sid?: string, token?: string, from?: string}  $config
     */
    public function __construct(protected array $config = []) {}

    /**
     * Send an OTP code to a destination phone number.
     *
     * @param  array<string, mixed>  $options
     */
    public function sendOtp(string $phone, string $otp, array $options = []): SmsResponse
    {
        $message = $options['message'] ?? "Your verification code is {$otp}.";

        return $this->sendSms($phone, $message, $options);
    }

    /**
     * Send a general SMS message to a destination phone number.
     *
     * @param  array<string, mixed>  $options
     */
    public function sendSms(string $phone, string $message, array $options = []): SmsResponse
    {
        $sid = trim($this->config['sid'] ?? '');
        $token = trim($this->config['token'] ?? '');
        $from = trim($this->config['from'] ?? '');

        if ($sid === '' || $token === '' || $from === '') {
            return SmsResponse::failure('Twilio credentials (Account SID, Auth Token, From Number) are not configured.');
        }

        try {
            $endpoint = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";

            $response = Http::withBasicAuth($sid, $token)
                ->asForm()
                ->post($endpoint, [
                    'To' => $phone,
                    'From' => $from,
                    'Body' => $message,
                ]);

            $json = $response->json() ?? [];

            if ($response->successful()) {
                return SmsResponse::success(
                    messageId: $json['sid'] ?? null,
                    rawPayload: $json
                );
            }

            $errorMessage = $json['message'] ?? ('Twilio error: HTTP '.$response->status());

            return SmsResponse::failure($errorMessage, $json);
        } catch (Throwable $e) {
            return SmsResponse::failure('Twilio connection error: '.$e->getMessage());
        }
    }

    public function getName(): string
    {
        return 'twilio';
    }
}
