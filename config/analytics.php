<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Analytics Master Enablement Switch
    |--------------------------------------------------------------------------
    |
    | When disabled, page tracking middleware and behavioral telemetry endpoints
    | bypass all database queries and processing, ensuring zero overhead.
    |
    */
    'enabled' => env('ANALYTICS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Log Retention (Days)
    |--------------------------------------------------------------------------
    |
    | Number of days of visitor logs to retain before automated daily pruning.
    |
    */
    'retention_days' => env('ANALYTICS_RETENTION_DAYS', 90),

    /*
    |--------------------------------------------------------------------------
    | Anonymize IP Addresses
    |--------------------------------------------------------------------------
    |
    | Mask the last octet of IPv4 addresses (192.168.1.0) and interface ID
    | of IPv6 addresses for privacy and compliance.
    |
    */
    'anonymize_ip' => env('ANALYTICS_ANONYMIZE_IP', true),

];
