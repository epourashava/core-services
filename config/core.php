<?php

return [
    /**
     * Core application base URL
     */
    'base_url' => env('CORE_BASE_URL', 'http://localhost:8000'),

    /**
     * Core application API URL
     */
    'api_url' => env(
        'CORE_API_URL',
        env('CORE_BASE_URL', 'http://localhost:8000') . '/api'
    ),

];
