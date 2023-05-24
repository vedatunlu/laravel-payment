<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sipay credentials
    |--------------------------------------------------------------------------
    |
    |
    */

    'credentials' => [
        'host' => env('SIPAY_HOST', null),
        'merchant_id' => env('SIPAY_MERCHANT_ID', null),
        'merchant_key' => env('SIPAY_MERCHANT_KEY', null),
        'app_key' => env('SIPAY_APP_KEY', null),
        'app_secret' => env('SIPAY_APP_SECRET', null),
    ]
];
