<?php

namespace App\Services\Otp\Drivers;

use App\Services\Otp\Contracts\SmsGatewayInterface;
use App\Services\Otp\Data\SmsResponse;
use Illuminate\Support\Facades\Http;
use Throwable;

class CustomHttpSmsDriver implements SmsGatewayInterface
{
    /**
     * @param  array{url?: string, method?: string, headers?: string|array<string, string>, payload?: string|array<string, mixed>, success_keyword?: string}  $config
     */
    public function __construct(protected array $config = []) {}

    /**
     * Send an OTP code to a destination phone number.
     *
     * @param  array<string, mixed>  $options
     */
    public function sendOtp(string $phone, string $otp, array $options = []): SmsResponse
    {
        $message = $options['message'] ?? "Your OTP code is {$otp}";

        return $this->sendRequest($phone, $message, $otp, $options);
    }

    /**
     * Send a general SMS message to a destination phone number.
     *
     * @param  array<string, mixed>  $options
     */
    public function sendSms(string $phone, string $message, array $options = []): SmsResponse
    {
        $otp = (string) ($options['otp'] ?? '');

        return $this->sendRequest($phone, $message, $otp, $options);
    }

    /**
     * Execute the custom HTTP gateway request with dynamic token replacement.
     *
     * @param  array<string, mixed>  $options
     */
    protected function sendRequest(string $phone, string $message, string $otp, array $options = []): SmsResponse
    {
        $rawUrl = trim($this->config['url'] ?? '');

        if ($rawUrl === '') {
            return SmsResponse::failure('Custom SMS Gateway URL is not configured.');
        }

        $method = strtoupper(trim($this->config['method'] ?? 'POST'));
        $successKeyword = trim($this->config['success_keyword'] ?? '');

        // Token replacements
        $replacements = [
            '{phone}' => $phone,
            '{raw_phone}' => preg_replace('/[^0-9]/', '', $phone) ?? '',
            '{otp}' => $otp,
            '{message}' => $message,
            '{url_encoded_message}' => urlencode($message),
        ];

        $finalUrl = str_replace(array_keys($replacements), array_values($replacements), $rawUrl);

        // Parse headers
        $headers = [];
        $rawHeaders = $this->config['headers'] ?? [];
        if (is_string($rawHeaders) && filled($rawHeaders)) {
            $decoded = json_decode($rawHeaders, true);
            if (is_array($decoded)) {
                $headers = $decoded;
            }
        } elseif (is_array($rawHeaders)) {
            $headers = $rawHeaders;
        }

        // Replace tokens in headers
        foreach ($headers as $headerKey => $headerValue) {
            if (is_string($headerValue)) {
                $headers[$headerKey] = str_replace(array_keys($replacements), array_values($replacements), $headerValue);
            }
        }

        try {
            $client = Http::withHeaders($headers)->timeout(15);

            if ($method === 'GET') {
                $response = $client->get($finalUrl);
            } else {
                $rawPayload = $this->config['payload'] ?? '';
                if (is_string($rawPayload) && filled($rawPayload)) {
                    $interpolatedPayload = str_replace(array_keys($replacements), array_values($replacements), $rawPayload);
                    $decodedPayload = json_decode($interpolatedPayload, true);

                    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedPayload)) {
                        $response = $client->asJson()->post($finalUrl, $decodedPayload);
                    } else {
                        // Form or plain string body
                        $response = $client->withBody($interpolatedPayload, 'text/plain')->post($finalUrl);
                    }
                } elseif (is_array($rawPayload) && count($rawPayload) > 0) {
                    $jsonString = json_encode($rawPayload);
                    $interpolated = str_replace(array_keys($replacements), array_values($replacements), (string) $jsonString);
                    $decodedPayload = json_decode($interpolated, true) ?? [];
                    $response = $client->asJson()->post($finalUrl, $decodedPayload);
                } else {
                    $response = $client->post($finalUrl);
                }
            }

            $body = $response->body();
            $json = $response->json() ?? ['raw_body' => $body];

            $isSuccessful = $response->successful();

            if ($isSuccessful && $successKeyword !== '') {
                $isSuccessful = str_contains($body, $successKeyword);
            }

            if ($isSuccessful) {
                return SmsResponse::success(
                    messageId: 'custom-'.uniqid(),
                    rawPayload: is_array($json) ? $json : ['raw_body' => $body]
                );
            }

            $errorMessage = "Custom Gateway failed with status {$response->status()}: ".substr($body, 0, 300);

            return SmsResponse::failure($errorMessage, is_array($json) ? $json : ['raw_body' => $body]);
        } catch (Throwable $e) {
            return SmsResponse::failure('Custom Gateway connection error: '.$e->getMessage());
        }
    }

    public function getName(): string
    {
        return 'custom_http';
    }
}
