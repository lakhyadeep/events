<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default SMS / OTP Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "log", "twilio", "msg91", "fast2sms", "custom_http"
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
    | Provider Configurations (Fallback if not configured in database)
    |--------------------------------------------------------------------------
    */
    'drivers' => [
        'twilio' => [
            'sid' => env('TWILIO_SID', ''),
            'token' => env('TWILIO_AUTH_TOKEN', ''),
            'from' => env('TWILIO_FROM_NUMBER', ''),
        ],

        'msg91' => [
            'auth_key' => env('MSG91_AUTH_KEY', ''),
            'sender_id' => env('MSG91_SENDER_ID', ''),
            'template_id' => env('MSG91_TEMPLATE_ID', ''),
        ],

        'fast2sms' => [
            'api_key' => env('FAST2SMS_API_KEY', ''),
            'route' => env('FAST2SMS_ROUTE', 'otp'), // "otp" or "dlt"
            'sender_id' => env('FAST2SMS_SENDER_ID', ''),
            'entity_id' => env('FAST2SMS_ENTITY_ID', ''),
        ],

        'custom_http' => [
            'url' => env('CUSTOM_SMS_URL', ''),
            'method' => env('CUSTOM_SMS_METHOD', 'POST'), // POST or GET
            'headers' => env('CUSTOM_SMS_HEADERS', '{}'),
            'payload' => env('CUSTOM_SMS_PAYLOAD', '{"phone":"{phone}","message":"{message}","otp":"{otp}"}'),
            'success_keyword' => env('CUSTOM_SMS_SUCCESS_KEYWORD', ''),
        ],
    ],
];
