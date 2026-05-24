<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Production Service Requirements
    |--------------------------------------------------------------------------
    |
    | Local and testing environments may use lightweight database/log/null
    | drivers. Production is expected to run the real infrastructure so
    | deploys fail early instead of silently falling back.
    |
    */

    'production' => [
        'enforce_services' => env('BLACK_SKY_ENFORCE_PRODUCTION_SERVICES', true),

        'required' => [
            'queue_connection' => env('BLACK_SKY_PRODUCTION_QUEUE_CONNECTION', 'redis'),
            'cache_store' => env('BLACK_SKY_PRODUCTION_CACHE_STORE', 'redis'),
            'session_driver' => env('BLACK_SKY_PRODUCTION_SESSION_DRIVER', 'redis'),
            'search_driver' => env('BLACK_SKY_PRODUCTION_SEARCH_DRIVER', 'meilisearch'),
        ],

        'disallowed_mailers' => ['array', 'log'],
        'require_meilisearch_key' => env('BLACK_SKY_PRODUCTION_REQUIRE_MEILISEARCH_KEY', true),
        'require_mail_from_address' => env('BLACK_SKY_PRODUCTION_REQUIRE_MAIL_FROM_ADDRESS', true),
    ],

    'search' => [
        'public_result_limit' => (int) env('PUBLIC_SEARCH_RESULT_LIMIT', 1000),
        'external_drivers' => ['algolia', 'meilisearch', 'typesense'],
    ],

];
