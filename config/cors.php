<?php

return [
    // Izinkan semua jalur
    'paths' => ['api/*', 'login', 'register', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // PENTING: Pakai bintang '*' agar browser tidak rewel
    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
