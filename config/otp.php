<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default SMS / OTP Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "log"
    |
    */
    'default' => env('OTP_SMS_DRIVER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | General OTP Parameters
    |--------------------------------------------------------------------------
    */
    'length' => (int) env('OTP_LENGTH', 6),
    'expiry_minutes' => (int) env('OTP_EXPIRY_MINUTES', 5),
    'resend_cooldown_seconds' => (int) env('OTP_RESEND_COOLDOWN', 60),
    'max_attempts' => (int) env('OTP_MAX_ATTEMPTS', 5),

    'message_template' => env('OTP_MESSAGE_TEMPLATE', 'Your verification code is :otp. Valid for :minutes minutes. Please do not share this code.'),

    /*
    |--------------------------------------------------------------------------
    | Master OTP for Testing & QA
    |--------------------------------------------------------------------------
    */
    'master_otp' => env('OTP_MASTER_CODE', null),
    'test_phone_numbers' => array_filter(explode(',', env('OTP_TEST_PHONES', ''))),

    /*
    |--------------------------------------------------------------------------
    | Provider Configurations
    |--------------------------------------------------------------------------
    */
    'drivers' => [
        'log' => [],
    ],
];
