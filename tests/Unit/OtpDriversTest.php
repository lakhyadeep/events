<?php

namespace Tests\Unit;

use App\Services\Otp\Contracts\SmsGatewayInterface;
use App\Services\Otp\Data\SmsResponse;
use App\Services\Otp\Drivers\LogSmsDriver;
use App\Services\Otp\OtpManager;
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
