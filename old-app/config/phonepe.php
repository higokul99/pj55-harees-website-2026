<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PhonePe Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains all PhonePe Payment Gateway related configuration.
    | Make sure to set all values in your .env file.
    |
    */

    'environment' => env('PHONEPE_ENVIRONMENT', 'PRODUCTION'),

    'client_id' => env('PHONEPE_CLIENT_ID'),

    'client_secret' => env('PHONEPE_CLIENT_SECRET'),

    'client_version' => env('PHONEPE_CLIENT_VERSION'),

    'merchant_id' => env('PHONEPE_MERCHANT_ID'),

    'webhook_username' => env('PHONEPE_WEBHOOK_USERNAME'),

    'webhook_password' => env('PHONEPE_WEBHOOK_PASSWORD'),

    'enable_logging' => env('PHONEPE_ENABLE_LOGGING', false),

    'callback_url' => env('PHONEPE_CALLBACK_URL'),

    'redirect_url' => env('PHONEPE_REDIRECT_URL'),

    /*
    |--------------------------------------------------------------------------
    | PhonePe API Endpoints
    |--------------------------------------------------------------------------
    */

    'api_endpoints' => [
        'production' => [
            'auth' => 'https://api.phonepe.com/apis/identity-manager',
            'payment' => 'https://api.phonepe.com/apis/pg',
        ],
        'uat' => [
            'auth' => 'https://api-preprod.phonepe.com/apis/identity-manager',
            'payment' => 'https://api-preprod.phonepe.com/apis/pgsandbox',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Configuration
    |--------------------------------------------------------------------------
    */

    'payment' => [
        'expire_after' => env('PHONEPE_EXPIRE_AFTER', 1800), // 30 minutes in seconds
        'min_amount' => 100, // Minimum amount in paisa (₹1.00)
    ],

    /*
    |--------------------------------------------------------------------------
    | Reconciliation Configuration
    |--------------------------------------------------------------------------
    */

    'reconciliation' => [
        'enabled' => env('PHONEPE_RECONCILIATION_ENABLED', true),
        'schedule' => env('PHONEPE_RECONCILIATION_SCHEDULE', '*/1 * * * *'), // Every minute
    ],
];
