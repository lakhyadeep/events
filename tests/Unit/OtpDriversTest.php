<?php

namespace Tests\Unit;

use App\Services\Otp\Contracts\SmsGatewayInterface;
use App\Services\Otp\Data\SmsResponse;
use App\Services\Otp\Drivers\Fast2SmsDriver;
use App\Services\Otp\Drivers\LogSmsDriver;
use App\Services\Otp\OtpManager;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Tests\TestCase;

class OtpDriversTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        LogSmsDriver::clearSentMessages();
    }

    public function test_log_sms_driver_records_dispatched_messages(): void
    {
        $driver = new LogSmsDriver;
        $response = $driver->sendOtp('9876543210', '123456');

        $this->assertTrue($response->successful);
        $this->assertCount(1, LogSmsDriver::$sentMessages);
        $this->assertSame('9876543210', LogSmsDriver::$sentMessages[0]['phone']);
        $this->assertStringContainsString('123456', LogSmsDriver::$sentMessages[0]['message']);
        $this->assertSame('log', $driver->getName());
    }

    public function test_log_sms_driver_sends_custom_sms(): void
    {
        $driver = new LogSmsDriver;
        $response = $driver->sendSms('9876543210', 'Hello from Dibrugarh Events');

        $this->assertTrue($response->successful);
        $this->assertCount(1, LogSmsDriver::$sentMessages);
        $this->assertSame('Hello from Dibrugarh Events', LogSmsDriver::$sentMessages[0]['message']);
    }

    public function test_otp_manager_resolves_log_driver(): void
    {
        $manager = new OtpManager;
        $driver = $manager->driver('log');

        $this->assertInstanceOf(LogSmsDriver::class, $driver);
        $this->assertSame('log', $driver->getName());
    }

    public function test_otp_manager_resolves_log_sms_alias(): void
    {
        $manager = new OtpManager;
        $driver = $manager->driver('log_sms');

        $this->assertInstanceOf(LogSmsDriver::class, $driver);
        $this->assertSame('log', $driver->getName());
    }

    public function test_fast2sms_driver_sends_otp_using_quick_otp_route(): void
    {
        Http::fake([
            'www.fast2sms.com/*' => Http::response([
                'return' => true,
                'request_id' => ['F2S_REQ_12345'],
                'message' => ['SMS sent successfully.'],
            ], 200),
        ]);

        $driver = new Fast2SmsDriver([
            'api_key' => 'TEST_FAST2SMS_KEY',
            'route' => 'otp',
        ]);

        $response = $driver->sendOtp('+919876543210', '456789');

        $this->assertTrue($response->successful);
        $this->assertSame('F2S_REQ_12345', $response->messageId);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'bulkV2')
                && $request->hasHeader('authorization', 'TEST_FAST2SMS_KEY')
                && $request['route'] === 'otp'
                && $request['variables_values'] === '456789'
                && $request['numbers'] === '9876543210';
        });
    }

    public function test_fast2sms_driver_sends_otp_using_dlt_route(): void
    {
        Http::fake([
            'www.fast2sms.com/*' => Http::response([
                'return' => true,
                'request_id' => 'F2S_DLT_67890',
                'message' => ['SMS sent successfully.'],
            ], 200),
        ]);

        $driver = new Fast2SmsDriver([
            'api_key' => 'TEST_FAST2SMS_KEY',
            'route' => 'dlt',
            'sender_id' => 'DIBEVT',
            'entity_id' => '12071612345678',
        ]);

        $response = $driver->sendOtp('9876543210', '112233');

        $this->assertTrue($response->successful);
        $this->assertSame('F2S_DLT_67890', $response->messageId);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'bulkV2')
                && $request['route'] === 'dlt'
                && $request['sender_id'] === 'DIBEVT'
                && $request['message'] === '12071612345678'
                && $request['variables_values'] === '112233'
                && $request['numbers'] === '9876543210';
        });
    }

    public function test_fast2sms_driver_sends_general_sms(): void
    {
        Http::fake([
            'www.fast2sms.com/*' => Http::response([
                'return' => true,
                'request_id' => ['F2S_SMS_999'],
                'message' => ['SMS sent successfully.'],
            ], 200),
        ]);

        $driver = new Fast2SmsDriver([
            'api_key' => 'TEST_FAST2SMS_KEY',
        ]);

        $response = $driver->sendSms('09876543210', 'Test greeting from Dibrugarh Events');

        $this->assertTrue($response->successful);
        $this->assertSame('F2S_SMS_999', $response->messageId);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'bulkV2')
                && $request['route'] === 'q'
                && $request['message'] === 'Test greeting from Dibrugarh Events'
                && $request['language'] === 'english'
                && $request['numbers'] === '9876543210';
        });
    }

    public function test_fast2sms_driver_handles_missing_api_key(): void
    {
        $driver = new Fast2SmsDriver([
            'api_key' => '',
        ]);

        $response = $driver->sendOtp('9876543210', '123456');

        $this->assertFalse($response->successful);
        $this->assertStringContainsString('Fast2SMS API Key is not configured', $response->error);
    }

    public function test_fast2sms_driver_handles_invalid_phone_number(): void
    {
        $driver = new Fast2SmsDriver([
            'api_key' => 'TEST_KEY',
        ]);

        $response = $driver->sendOtp('12345', '123456');

        $this->assertFalse($response->successful);
        $this->assertStringContainsString('Invalid Indian mobile number', $response->error);
    }

    public function test_fast2sms_driver_handles_api_failure_response(): void
    {
        Http::fake([
            'www.fast2sms.com/*' => Http::response([
                'return' => false,
                'status_code' => 411,
                'message' => 'Invalid Numbers',
            ], 200),
        ]);

        $driver = new Fast2SmsDriver([
            'api_key' => 'TEST_KEY',
        ]);

        $response = $driver->sendOtp('9876543210', '123456');

        $this->assertFalse($response->successful);
        $this->assertSame('Invalid Numbers', $response->error);
    }

    public function test_fast2sms_driver_handles_connection_exception(): void
    {
        Http::fake([
            'www.fast2sms.com/*' => Http::failedConnection(),
        ]);

        $driver = new Fast2SmsDriver([
            'api_key' => 'TEST_KEY',
        ]);

        $response = $driver->sendOtp('9876543210', '123456');

        $this->assertFalse($response->successful);
        $this->assertStringContainsString('Fast2SMS connection error', $response->error);
    }

    public function test_otp_manager_resolves_fast2sms_driver(): void
    {
        config(['otp.drivers.fast2sms.api_key' => 'SAMPLE_F2S_KEY']);

        $manager = new OtpManager;
        $driver = $manager->driver('fast2sms');

        $this->assertInstanceOf(Fast2SmsDriver::class, $driver);
        $this->assertSame('fast2sms', $driver->getName());
    }

    public function test_otp_manager_throws_exception_on_unsupported_driver(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported OTP/SMS gateway driver [unsupported_driver].');

        $manager = new OtpManager;
        $manager->driver('unsupported_driver');
    }

    public function test_otp_manager_supports_custom_driver_extension(): void
    {
        $manager = new OtpManager;

        $customMock = new class implements SmsGatewayInterface
        {
            public function sendOtp(string $phone, string $otp, array $options = []): SmsResponse
            {
                return SmsResponse::success('custom-ext-1');
            }

            public function sendSms(string $phone, string $message, array $options = []): SmsResponse
            {
                return SmsResponse::success('custom-ext-1');
            }

            public function getName(): string
            {
                return 'verified_provider';
            }
        };

        $manager->extend('verified_provider', fn () => $customMock);

        $driver = $manager->driver('verified_provider');
        $this->assertSame('verified_provider', $driver->getName());

        $response = $driver->sendOtp('9999999999', '112233');
        $this->assertTrue($response->successful);
    }
}
