<?php

namespace App\Services\Otp;

use App\Models\OtpSetting;
use App\Services\Otp\Contracts\SmsGatewayInterface;
use App\Services\Otp\Drivers\Fast2SmsDriver;
use App\Services\Otp\Drivers\LogSmsDriver;
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
     * Safely obtain the active OtpSetting model or null if database is unmigrated/unavailable.
     */
    protected function getOtpSetting(): ?OtpSetting
    {
        try {
            return OtpSetting::current();
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Get the default driver name configured in system settings.
     */
    public function getDefaultDriver(): string
    {
        return $this->getOtpSetting()?->active_driver ?: config('otp.default', 'log');
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
     * Create an instance of the Log SMS driver (alias).
     */
    protected function createLogSmsDriver(): LogSmsDriver
    {
        return new LogSmsDriver;
    }

    /**
     * Create an instance of the Fast2SMS driver.
     */
    protected function createFast2smsDriver(): Fast2SmsDriver
    {
        $setting = $this->getOtpSetting();
        $dbConfig = $setting?->fast2sms_settings ?? [];
        $fileConfig = config('otp.drivers.fast2sms', []);

        $config = array_merge(
            is_array($fileConfig) ? $fileConfig : [],
            is_array($dbConfig) ? array_filter($dbConfig, fn ($val) => filled($val)) : []
        );

        return new Fast2SmsDriver($config);
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
