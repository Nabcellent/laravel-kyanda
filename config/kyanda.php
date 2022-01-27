<?php

return [
    /*
     |------------------------------------------------------
     | Set sandbox mode
     | ------------------------------------------------------
     | Specify whether this is a test app or production app
     |
     | NB:  This is in case of a future test sandbox.
     |
     | Sandbox base url:
     | Production base url: https://api.kyanda.app
     */
    'sandbox' => env('KYANDA_SANDBOX', false),

    /*
   |--------------------------------------------------------------------------
   | Cache credentials
   |--------------------------------------------------------------------------
   |
   | If you decide to cache credentials, they will be kept in your app cache
   | configuration for some time. Reducing the need for many requests for
   | generating credentials
   |
   */
    'cache_credentials' => true,

    /*
   |--------------------------------------------------------------------------
   | URLs
   |--------------------------------------------------------------------------
   |
   | Callback - Will be registered with Kyanda and used to send you payment notifications.
   |
   */
    'urls' => [
        'base' => 'https://api.kyanda.app',
        /*
         * --------------------------------------------------------------------------------------
         * Callbacks:
         * ---------------------------------------------------------------------------------------
         * Please update your app url in .env file
         * Note: This package has already routes for handling this callback.
         * You should leave this values as they are unless you know what you are doing.
         */
        'callback' => env('APP_URL') . '/kyanda/callbacks/notification',
    ],

    /*
   |--------------------------------------------------------------------------
   | Merchant ID
   |--------------------------------------------------------------------------
   |
   | Provided by Kyanda after account creation.
   |
   */
    'merchant_id' => env('KYANDA_MERCHANT_ID'),


    /*
   |--------------------------------------------------------------------------
   | API Key
   |--------------------------------------------------------------------------
   |
   | Provided by Kyanda after account creation.
   |
   */
    'api_key' => env('KYANDA_API_KEY'),


    /*
   |--------------------------------------------------------------------------
   | LIMITS
   |--------------------------------------------------------------------------
   |
   | Limits - Will be given by Kyanda and used to validate
   |
   */
    'limits' => [
        'AIRTIME.min' => 10,
        'AIRTIME.max' => 10000,

        'bills' => [
            'KPLC_POSTPAID.min' => 100,
            'KPLC_POSTPAID.max' => 35000,

            'KPLC_PREPAID.min' => 100,
            'KPLC_PREPAID.max' => 35000,
        ]
    ],
];