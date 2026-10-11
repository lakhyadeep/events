<?php

namespace App\Services\Otp\Drivers;

use App\Services\Otp\Contracts\SmsGatewayInterface;
use App\Services\Otp\Data\SmsResponse;
use Illuminate\Support\Facades\Http;
use Throwable;

class Fast2SmsDriver implements SmsGatewayInterface
{
    /**
     * @param  array{api_key?: string, route?: string, sender_id?: string, entity_id?: string}  $config
     */
    public function __construct(protected array $config = []) {}

    /**
     * Send an OTP code to a destination phone number.
     *
     * @param  array<string, mixed>  $options
     */
    public function sendOtp(string $phone, string $otp, array $options = []): SmsResponse
    {
        $apiKey = trim($this->config['api_key'] ?? '');

        if ($apiKey === '') {
            return SmsResponse::failure('Fast2SMS API Key is not configured.');
        }

        $cleanPhone = $this->formatMobileNumber($phone);
        $route = strtolower(trim($this->config['route'] ?? 'otp'));

        try {
            $endpoint = 'https://www.fast2sms.com/dev/bulkV2';

            $payload = [
                'route' => $route === 'dlt' ? 'dlt' : 'otp',
                'variables_values' => $otp,
                'numbers' => $cleanPhone,
            ];

            if ($route === 'dlt') {
                $payload['sender_id'] = $this->config['sender_id'] ?? '';
                $payload['message'] = $this->config['entity_id'] ?? '';
            }

            $response = Http::withHeaders([
                'authorization' => $apiKey,
                'accept' => 'application/json',
            ])->post($endpoint, $payload);

            $json = $response->json() ?? [];

            if ($response->successful() && ($json['return'] ?? false) === true) {
                $requestIds = $json['request_id'] ?? null;
                $messageId = is_array($requestIds) ? implode(',', $requestIds) : (string) $requestIds;

                return SmsResponse::success(
                    messageId: $messageId,
                    rawPayload: $json
                );
            }

            $errorMessage = $json['message'][0] ?? ($json['message'] ?? ('Fast2SMS error: HTTP '.$response->status()));

            return SmsResponse::failure((string) $errorMessage, $json);
        } catch (Throwable $e) {
            return SmsResponse::failure('Fast2SMS connection error: '.$e->getMessage());
        }
    }

    /**
     * Send a general SMS message to a destination phone number.
     *
     * @param  array<string, mixed>  $options
     */
    public function sendSms(string $phone, string $message, array $options = []): SmsResponse
    {
        $apiKey = trim($this->config['api_key'] ?? '');

        if ($apiKey === '') {
            return SmsResponse::failure('Fast2SMS API Key is not configured.');
        }

        $cleanPhone = $this->formatMobileNumber($phone);

        try {
            $endpoint = 'https://www.fast2sms.com/dev/bulkV2';

            $response = Http::withHeaders([
                'authorization' => $apiKey,
                'accept' => 'application/json',
            ])->post($endpoint, [
                'route' => 'q',
                'message' => $message,
                'language' => 'english',
                'numbers' => $cleanPhone,
            ]);

            $json = $response->json() ?? [];

            if ($response->successful() && ($json['return'] ?? false) === true) {
                $requestIds = $json['request_id'] ?? null;
                $messageId = is_array($requestIds) ? implode(',', $requestIds) : (string) $requestIds;

                return SmsResponse::success(
                    messageId: $messageId,
                    rawPayload: $json
                );
            }

            $errorMessage = $json['message'][0] ?? ($json['message'] ?? ('Fast2SMS error: HTTP '.$response->status()));

            return SmsResponse::failure((string) $errorMessage, $json);
        } catch (Throwable $e) {
            return SmsResponse::failure('Fast2SMS connection error: '.$e->getMessage());
        }
    }

    /**
     * Normalize Indian 10-digit mobile number for Fast2SMS.
     */
    protected function formatMobileNumber(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone) ?? '';

        if (strlen($digits) > 10 && str_starts_with($digits, '91')) {
            return substr($digits, 2);
        }

        return $digits;
    }

    public function getName(): string
    {
        return 'fast2sms';
    }
}
