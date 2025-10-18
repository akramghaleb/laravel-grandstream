<?php

// config for AkramGhaleb/LaravelGrandstream
return [
    /*
    |--------------------------------------------------------------------------
    | Grandstream UCM connection
    |--------------------------------------------------------------------------
    */
    'base' => env('UCM_BASE', 'http://127.0.0.1:8089'),
    'user' => env('UCM_API_USER', 'admin'),
    'pass' => env('UCM_API_PASS', ''),
    'version' => env('UCM_API_VER', '1.2'),
    'cookie_ttl' => (int) env('UCM_COOKIE_TTL', 9), // minutes

    /*
    |--------------------------------------------------------------------------
    | HTTP options
    |--------------------------------------------------------------------------
    */
    'verify_ssl' => (bool) env('UCM_VERIFY_SSL', false),
    'timeout' => (int) env('UCM_HTTP_TIMEOUT', 15),
    'retries' => (int) env('UCM_HTTP_RETRIES', 2),
    'retry_delay_ms' => (int) env('UCM_HTTP_RETRY_DELAY_MS', 300),

    /*
    |--------------------------------------------------------------------------
    | Optional: multi-tenant resolver
    |--------------------------------------------------------------------------
    | If you need per-tenant cookies, set a callable here or in a service.
    | Example:  'tenant_resolver' => fn () => auth()->id() ?? 'default',
    */
    'tenant_resolver' => null,

    /*
    |--------------------------------------------------------------------------
    | Webhook basic auth (for Grandstream push events)
    |--------------------------------------------------------------------------
    */
    'webhook' => [
        'user' => env('GRANDSTREAM_WEBHOOK_USER'), // e.g. gscdr
        'pass' => env('GRANDSTREAM_WEBHOOK_PASS'), // e.g. gscdr123
    ],
];
