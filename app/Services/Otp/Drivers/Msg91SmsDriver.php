<?php

namespace App\Services\Otp\Drivers;

use App\Services\Otp\Contracts\SmsGatewayInterface;
use App\Services\Otp\Data\SmsResponse;
use Illuminate\Support\Facades\Http;
use Throwable;

class Msg91SmsDriver implements SmsGatewayInterface
{
    /**
     * @param  array{auth_key?: string, sender_id?: string, template_id?: string}  $config
     */
    public function __construct(protected array $config = []) {}

    /**
     * Send an OTP code to a destination phone number.
     *
     * @param  array<string, mixed>  $options
     */
    public function sendOtp(string $phone, string $otp, array $options = []): SmsResponse
    {
        $authKey = trim($this->config['auth_key'] ?? '');
        $templateId = trim($this->config['template_id'] ?? '');

        if ($authKey === '' || $templateId === '') {
            return SmsResponse::failure('MSG91 credentials (Auth Key, Template ID) are not configured.');
        }

        $formattedPhone = $this->formatMobileNumber($phone);

        try {
            $endpoint = 'https://control.msg91.com/api/v5/otp';

            $queryParams = [
                'template_id' => $templateId,
                'mobile' => $formattedPhone,
                'authkey' => $authKey,
                'otp' => $otp,
            ];

            $response = Http::withHeaders([
                'authkey' => $authKey,
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ])->get($endpoint, $queryParams);

            $json = $response->json() ?? [];

            if ($response->successful() && ($json['type'] ?? '') !== 'error') {
                return SmsResponse::success(
                    messageId: $json['message'] ?? null,
                    rawPayload: $json
                );
            }

            $errorMessage = $json['message'] ?? ('MSG91 error: HTTP '.$response->status());

            return SmsResponse::failure($errorMessage, $json);
        } catch (Throwable $e) {
            return SmsResponse::failure('MSG91 connection error: '.$e->getMessage());
        }
    }

    /**
     * Send a general SMS message to a destination phone number.
     *
     * @param  array<string, mixed>  $options
     */
    public function sendSms(string $phone, string $message, array $options = []): SmsResponse
    {
        $authKey = trim($this->config['auth_key'] ?? '');
        $senderId = trim($this->config['sender_id'] ?? '');
        $templateId = trim($this->config['template_id'] ?? '');

        if ($authKey === '' || $templateId === '') {
            return SmsResponse::failure('MSG91 credentials (Auth Key, Template ID) are not configured.');
        }

        $formattedPhone = $this->formatMobileNumber($phone);

        try {
            $endpoint = 'https://control.msg91.com/api/v5/flow';

            $response = Http::withHeaders([
                'authkey' => $authKey,
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ])->post($endpoint, [
                'template_id' => $templateId,
                'short_url' => '0',
                'sender' => $senderId,
                'recipients' => [
                    [
                        'mobiles' => $formattedPhone,
                        'message' => $message,
                    ],
                ],
            ]);

            $json = $response->json() ?? [];

            if ($response->successful() && ($json['type'] ?? '') !== 'error') {
                return SmsResponse::success(
                    messageId: $json['message'] ?? null,
                    rawPayload: $json
                );
            }

            $errorMessage = $json['message'] ?? ('MSG91 SMS error: HTTP '.$response->status());

            return SmsResponse::failure($errorMessage, $json);
        } catch (Throwable $e) {
            return SmsResponse::failure('MSG91 connection error: '.$e->getMessage());
        }
    }

    /**
     * Normalize mobile number for MSG91 (requires country code without +, e.g. 919876543210).
     */
    protected function formatMobileNumber(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone) ?? '';

        if (strlen($digits) === 10) {
            return '91'.$digits;
        }

        return $digits;
    }

    public function getName(): string
    {
        return 'msg91';
    }
}
