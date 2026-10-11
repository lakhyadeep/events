<?php

namespace App\Services\Otp;

use App\Models\OtpSetting;
use App\Services\Otp\Contracts\SmsGatewayInterface;
use App\Services\Otp\Drivers\CustomHttpSmsDriver;
use App\Services\Otp\Drivers\Fast2SmsDriver;
use App\Services\Otp\Drivers\LogSmsDriver;
use App\Services\Otp\Drivers\Msg91SmsDriver;
use App\Services\Otp\Drivers\TwilioSmsDriver;
use Closure;
use InvalidArgumentException;

class OtpManager
{
    /**
     * The array of resolved drivers.
     *
     * @var array<string, SmsGatewayInterface>
     */
    protected array $drivers = [];

    /**
     * The registered custom driver creators.
     *
     * @var array<string, Closure>
     */
    protected array $customCreators = [];

    /**
     * Get a driver instance by name or resolve default.
     */
    public function driver(?string $driver = null): SmsGatewayInterface
    {
        $driver = $driver ?: $this->getDefaultDriver();

        if (! isset($this->drivers[$driver])) {
            $this->drivers[$driver] = $this->resolve($driver);
        }

        return $this->drivers[$driver];
    }

    /**
     * Get the default driver name configured in system settings.
     */
    public function getDefaultDriver(): string
    {
        try {
            $setting = OtpSetting::current();

            return $setting->active_driver ?: config('otp.default', 'log');
        } catch (\Throwable) {
            return config('otp.default', 'log');
        }
    }

    /**
     * Resolve the given driver instance.
     */
    protected function resolve(string $driver): SmsGatewayInterface
    {
        if (isset($this->customCreators[$driver])) {
            return call_user_func($this->customCreators[$driver], $this);
        }

        $method = 'create'.str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $driver))).'Driver';

        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        throw new InvalidArgumentException("Unsupported OTP/SMS gateway driver [{$driver}].");
    }

    /**
     * Create an instance of the Log SMS driver.
     */
    protected function createLogDriver(): LogSmsDriver
    {
        return new LogSmsDriver;
    }

    /**
     * Create an instance of the Twilio SMS driver.
     */
    protected function createTwilioDriver(): TwilioSmsDriver
    {
        $settings = OtpSetting::current()->twilio_settings ?? [];
        if (empty($settings)) {
            $settings = config('otp.drivers.twilio', []);
        }

        return new TwilioSmsDriver($settings);
    }

    /**
     * Create an instance of the MSG91 SMS driver.
     */
    protected function createMsg91Driver(): Msg91SmsDriver
    {
        $settings = OtpSetting::current()->msg91_settings ?? [];
        if (empty($settings)) {
            $settings = config('otp.drivers.msg91', []);
        }

        return new Msg91SmsDriver($settings);
    }

    /**
     * Create an instance of the Fast2SMS driver.
     */
    protected function createFast2smsDriver(): Fast2SmsDriver
    {
        $settings = OtpSetting::current()->fast2sms_settings ?? [];
        if (empty($settings)) {
            $settings = config('otp.drivers.fast2sms', []);
        }

        return new Fast2SmsDriver($settings);
    }

    /**
     * Create an instance of the Custom HTTP SMS driver.
     */
    protected function createCustomHttpDriver(): CustomHttpSmsDriver
    {
        $settings = OtpSetting::current()->custom_http_settings ?? [];
        if (empty($settings)) {
            $settings = config('otp.drivers.custom_http', []);
        }

        return new CustomHttpSmsDriver($settings);
    }

    /**
     * Register a custom driver creator Closure.
     */
    public function extend(string $driver, Closure $callback): static
    {
        $this->customCreators[$driver] = $callback;
        unset($this->drivers[$driver]);

        return $this;
    }

    /**
     * Purge all cached driver instances.
     */
    public function purge(): void
    {
        $this->drivers = [];
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  array<int, mixed>  $parameters
     */
    public function __call(string $method, array $parameters): mixed
    {
        return $this->driver()->{$method}(...$parameters);
    }
}
