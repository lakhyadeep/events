<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mobile Number Format Policy
    |--------------------------------------------------------------------------
    | Enforce strict 10-digit Indian mobile number validation (starting with 6-9).
    */
    'strict_indian_mobile' => (bool) env('FRAUD_STRICT_INDIAN_MOBILE', true),

    /*
    |--------------------------------------------------------------------------
    | Device Multi-Phone Limits
    |--------------------------------------------------------------------------
    | Maximum distinct phone numbers allowed to vote from the same device /
    | browser visitor ID for a single event. Setting this to 3 allows family
    | members sharing a phone/tablet while blocking mass multi-vote abuse.
    */
    'max_phones_per_device' => (int) env('FRAUD_MAX_PHONES_PER_DEVICE', 3),

    /*
    |--------------------------------------------------------------------------
    | Disposable / Temporary SMS Number Blacklist
    |--------------------------------------------------------------------------
    | Numbers actively harvested from public temporary SMS portals
    | (e.g. Receive-SMS Online, Free SMS Online, Quackr, AnonymSMS, etc.)
    */
    'disposable_numbers' => [
        // Known public Indian virtual numbers frequently seen on free SMS portals
        '7011982736',
        '7428192837',
        '8826192834',
        '9818291029',
        '9910293847',
        '9319201928',
        '9582910293',
        '9821092834',
        '7838192039',
        '8448192038',
        '9289102938',
    ],

    /*
    |--------------------------------------------------------------------------
    | IP Time-Window Voting Restrictions
    |--------------------------------------------------------------------------
    | Limits the number of votes allowed from the same IP address within a
    | sliding time window. Prevents automated scripts and cyber-café bot farms
    | while automatically unblocking the IP once the time window expires.
    */
    'ip_restriction_enabled' => (bool) env('FRAUD_IP_RESTRICTION_ENABLED', true),

    'max_votes_per_ip' => (int) env('FRAUD_MAX_VOTES_PER_IP', 3),

    'ip_window_minutes' => (int) env('FRAUD_IP_WINDOW_MINUTES', 30),

    'ip_whitelist' => array_filter(array_map('trim', explode(',', (string) env('FRAUD_IP_WHITELIST', '')))),
];
