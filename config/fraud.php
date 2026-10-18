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
];
