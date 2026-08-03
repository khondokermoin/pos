<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SSLCommerz Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | These values are read from environment variables. The actual live
    | credentials are managed via the Super Admin → Settings → Payment page
    | and stored in the `settings` database table. The SubscriptionController
    | reads directly from the DB (Setting::get()) so these env-based values
    | serve as a fallback for local/CLI usage.
    |
    */

    'store_id'       => env('SSLCOMMERZ_STORE_ID', ''),

    'store_password' => env('SSLCOMMERZ_STORE_PASSWORD', ''),

    // env() returns strings, so cast properly: "false"/"0"/"no"/"off" → false
    'is_sandbox'     => ! in_array(strtolower((string) env('SSLCOMMERZ_IS_SANDBOX', 'true')), ['false', '0', 'no', 'off']),

];
