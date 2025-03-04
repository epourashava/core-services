<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Core Service Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the settings for Core service.
    */

    'core-oauth2' => [
        'base_url' => env('CORE_BASE_URL', 'http://localhost:8000'),
        'client_id' => env('CORE_CLIENT_ID', ''),
        'client_secret' => env('CORE_CLIENT_SECRET', ''),
        'redirect' => env('CORE_REDIRECT_URI', '/auth/callback'),
    ]

];
