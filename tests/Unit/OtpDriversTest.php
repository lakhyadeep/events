<?php

namespace Tests\Unit;

use App\Services\Otp\Contracts\SmsGatewayInterface;
use App\Services\Otp\Data\SmsResponse;
use App\Services\Otp\Drivers\CustomHttpSmsDriver;
use App\Services\Otp\Drivers\Fast2SmsDriver;
use App\Services\Otp\Drivers\LogSmsDriver;
use App\Services\Otp\Drivers\Msg91SmsDriver;
use App\Services\Otp\Drivers\TwilioSmsDriver;
use App\Services\Otp\OtpManager;
use Illuminate\Support\Facades\Http;
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
    }

    public function test_twilio_driver_sends_http_request(): void
    {
        Http::fake([
            'api.twilio.com/*' => Http::response(['sid' => 'SM12345', 'status' => 'queued'], 200),
        ]);

        $driver = new TwilioSmsDriver([
            'sid' => 'AC_TEST_SID',
            'token' => 'TEST_TOKEN',
            'from' => '+15551234567',
        ]);

        $response = $driver->sendOtp('+19876543210', '654321');

        $this->assertTrue($response->successful);
        $this->assertSame('SM12345', $response->messageId);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'AC_TEST_SID/Messages.json')
                && $request['To'] === '+19876543210'
                && $request['From'] === '+15551234567'
                && str_contains($request['Body'], '654321');
        });
    }

    public function test_msg91_driver_sends_http_request(): void
    {
        Http::fake([
            'control.msg91.com/*' => Http::response(['type' => 'success', 'message' => 'MSG_REQ_999'], 200),
        ]);

        $driver = new Msg91SmsDriver([
            'auth_key' => 'TEST_AUTH_KEY',
            'sender_id' => 'DIBEVT',
            'template_id' => 'FLOW_123',
        ]);

        $response = $driver->sendOtp('9876543210', '432198');

        $this->assertTrue($response->successful);
        $this->assertSame('MSG_REQ_999', $response->messageId);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'control.msg91.com/api/v5/otp')
                && $request->hasHeader('authkey', 'TEST_AUTH_KEY');
        });
    }

    public function test_fast2sms_driver_sends_http_request(): void
    {
        Http::fake([
            'www.fast2sms.com/*' => Http::response(['return' => true, 'request_id' => ['F2S_888']], 200),
        ]);

        $driver = new Fast2SmsDriver([
            'api_key' => 'FAST2SMS_KEY_XYZ',
            'route' => 'otp',
        ]);

        $response = $driver->sendOtp('9876543210', '777888');

        $this->assertTrue($response->successful);
        $this->assertSame('F2S_888', $response->messageId);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'bulkV2')
                && $request->hasHeader('authorization', 'FAST2SMS_KEY_XYZ')
                && $request['variables_values'] === '777888'
                && $request['numbers'] === '9876543210';
        });
    }

    public function test_custom_http_driver_interpolates_tokens_and_sends(): void
    {
        Http::fake([
            'custom-gateway.test/*' => Http::response('{"status":"OK","ref":"CG_100"}', 200),
        ]);

        $driver = new CustomHttpSmsDriver([
            'url' => 'https://custom-gateway.test/sms/dispatch?target={phone}',
            'method' => 'POST',
            'headers' => json_encode(['X-Api-Key' => 'SECRET_XYZ']),
            'payload' => json_encode(['mobile' => '{phone}', 'code' => '{otp}', 'msg' => '{message}']),
            'success_keyword' => 'OK',
        ]);

        $response = $driver->sendOtp('9876543210', '998877', ['message' => 'Your security code is 998877']);

        $this->assertTrue($response->successful);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'target=9876543210')
                && $request->hasHeader('X-Api-Key', 'SECRET_XYZ')
                && $request['mobile'] === '9876543210'
                && $request['code'] === '998877';
        });
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
                return 'my_vendor';
            }
        };

        $manager->extend('my_vendor', fn () => $customMock);

        $driver = $manager->driver('my_vendor');
        $this->assertSame('my_vendor', $driver->getName());

        $response = $driver->sendOtp('9999999999', '112233');
        $this->assertTrue($response->successful);
    }
}
