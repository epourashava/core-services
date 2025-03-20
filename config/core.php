<?php

use Core\Models\Municipality;

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

    /**
     * Core tenant model
     */
    'tenant_model' => Municipality::class,

    /**
     * Permissions enum class or array
     */
    'permissions' => [] // Permission::all() - App\Enums\Permission


];
