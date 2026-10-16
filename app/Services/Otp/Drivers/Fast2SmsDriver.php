<?php

namespace App\Services\Otp\Drivers;

use App\Services\Otp\Contracts\SmsGatewayInterface;
use App\Services\Otp\Data\SmsResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class Fast2SmsDriver implements SmsGatewayInterface
{
    /**
     * @param  array{
     *     api_key?: string,
     *     route?: string,
     *     sender_id?: string,
     *     entity_id?: string,
     *     endpoint?: string,
     * }  $config
     */
    public function __construct(protected array $config = []) {}

    /**
     * Send an OTP code to a destination phone number.
     *
     * @param  array<string, mixed>  $options
     */
    public function sendOtp(string $phone, string $otp, array $options = []): SmsResponse
    {
        $apiKey = trim((string) ($this->config['api_key'] ?? ''));

        if ($apiKey === '') {
            return SmsResponse::failure('Fast2SMS API Key is not configured.');
        }

        $cleanPhone = $this->formatMobileNumber($phone);

        if (strlen($cleanPhone) !== 10) {
            return SmsResponse::failure("Invalid Indian mobile number [{$phone}]. Fast2SMS requires a valid 10-digit number.");
        }

        $route = strtolower(trim((string) ($this->config['route'] ?? 'otp')));
        $endpoint = (string) ($this->config['endpoint'] ?? 'https://www.fast2sms.com/dev/bulkV2');

        if ($route === 'dlt') {
            $payload = [
                'route' => 'dlt',
                'sender_id' => (string) ($this->config['sender_id'] ?? ''),
                'message' => (string) ($this->config['entity_id'] ?? ''),
                'variables_values' => $otp,
                'numbers' => $cleanPhone,
            ];
        } elseif ($route === 'q') {
            $message = (string) ($options['message'] ?? "Your verification code is {$otp}");
            $payload = [
                'route' => 'q',
                'message' => $message,
                'language' => 'english',
                'numbers' => $cleanPhone,
            ];
        } else {
            $payload = [
                'route' => 'otp',
                'variables_values' => $otp,
                'numbers' => $cleanPhone,
            ];
        }

        try {
            $response = Http::withHeaders([
                'authorization' => $apiKey,
                'accept' => 'application/json',
            ])
                ->connectTimeout(5)
                ->timeout(15)
                ->post($endpoint, $payload);

            $json = $response->json() ?? [];

            if ($response->successful() && ($json['return'] ?? false) === true) {
                $requestIds = $json['request_id'] ?? null;
                $messageId = is_array($requestIds) ? implode(',', $requestIds) : (string) ($requestIds ?? '');

                return SmsResponse::success(
                    messageId: $messageId,
                    rawPayload: $json
                );
            }

            $errorMessage = '';
            if (! empty($json['message'])) {
                $errorMessage = is_array($json['message']) ? implode('; ', $json['message']) : (string) $json['message'];
            } elseif ($response->failed()) {
                $errorMessage = 'Fast2SMS error: HTTP '.$response->status();
            } else {
                $errorMessage = 'Unknown error response from Fast2SMS';
            }

            return SmsResponse::failure($errorMessage, $json);
        } catch (Throwable $e) {
            Log::error('Fast2SMS sendOtp exception: '.$e->getMessage(), [
                'phone' => $cleanPhone,
                'exception' => $e,
            ]);

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
        $apiKey = trim((string) ($this->config['api_key'] ?? ''));

        if ($apiKey === '') {
            return SmsResponse::failure('Fast2SMS API Key is not configured.');
        }

        $cleanPhone = $this->formatMobileNumber($phone);

        if (strlen($cleanPhone) !== 10) {
            return SmsResponse::failure("Invalid Indian mobile number [{$phone}]. Fast2SMS requires a valid 10-digit number.");
        }

        $endpoint = (string) ($this->config['endpoint'] ?? 'https://www.fast2sms.com/dev/bulkV2');

        try {
            $response = Http::withHeaders([
                'authorization' => $apiKey,
                'accept' => 'application/json',
            ])
                ->connectTimeout(5)
                ->timeout(15)
                ->post($endpoint, [
                    'route' => 'q',
                    'message' => $message,
                    'language' => 'english',
                    'numbers' => $cleanPhone,
                ]);

            $json = $response->json() ?? [];

            if ($response->successful() && ($json['return'] ?? false) === true) {
                $requestIds = $json['request_id'] ?? null;
                $messageId = is_array($requestIds) ? implode(',', $requestIds) : (string) ($requestIds ?? '');

                return SmsResponse::success(
                    messageId: $messageId,
                    rawPayload: $json
                );
            }

            $errorMessage = '';
            if (! empty($json['message'])) {
                $errorMessage = is_array($json['message']) ? implode('; ', $json['message']) : (string) $json['message'];
            } elseif ($response->failed()) {
                $errorMessage = 'Fast2SMS error: HTTP '.$response->status();
            } else {
                $errorMessage = 'Unknown error response from Fast2SMS';
            }

            return SmsResponse::failure($errorMessage, $json);
        } catch (Throwable $e) {
            Log::error('Fast2SMS sendSms exception: '.$e->getMessage(), [
                'phone' => $cleanPhone,
                'exception' => $e,
            ]);

            return SmsResponse::failure('Fast2SMS connection error: '.$e->getMessage());
        }
    }

    /**
     * Normalize Indian 10-digit mobile number for Fast2SMS.
     */
    public function formatMobileNumber(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone) ?? '';

        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            return substr($digits, 2);
        }

        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            return substr($digits, 1);
        }

        if (strlen($digits) > 10) {
            return substr($digits, -10);
        }

        return $digits;
    }

    /**
     * Check the current Fast2SMS wallet balance.
     *
     * @return array{
     *     successful: bool,
     *     balance: float|null,
     *     message: string,
     *     rawPayload?: array<string, mixed>,
     * }
     */
    public function checkWalletBalance(): array
    {
        $apiKey = trim((string) ($this->config['api_key'] ?? ''));

        if ($apiKey === '') {
            return [
                'successful' => false,
                'balance' => null,
                'message' => 'Fast2SMS API Key is not configured.',
            ];
        }

        try {
            $response = Http::withHeaders([
                'authorization' => $apiKey,
                'accept' => 'application/json',
            ])
                ->connectTimeout(5)
                ->timeout(10)
                ->post('https://www.fast2sms.com/dev/wallet');

            $json = $response->json() ?? [];

            if ($response->successful()) {
                if (isset($json['wallet'])) {
                    return [
                        'successful' => true,
                        'balance' => (float) $json['wallet'],
                        'message' => 'Fast2SMS wallet balance retrieved successfully.',
                        'rawPayload' => $json,
                    ];
                }
            }

            $errorMessage = '';
            if (! empty($json['message'])) {
                $errorMessage = is_array($json['message']) ? implode('; ', $json['message']) : (string) $json['message'];
            } elseif ($response->failed()) {
                $errorMessage = 'Fast2SMS error: HTTP '.$response->status();
            } else {
                $errorMessage = 'Unable to parse wallet balance from Fast2SMS.';
            }

            return [
                'successful' => false,
                'balance' => null,
                'message' => $errorMessage,
                'rawPayload' => $json,
            ];
        } catch (Throwable $e) {
            Log::warning('Fast2SMS checkWalletBalance exception: '.$e->getMessage());

            return [
                'successful' => false,
                'balance' => null,
                'message' => 'Fast2SMS connection error: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Get the identifier name of the gateway driver.
     */
    public function getName(): string
    {
        return 'fast2sms';
    }
}
