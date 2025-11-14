<?php

return [
    'paths' => ['api/*', 'storage/*', 'public/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'], // kamu bisa ubah ke domain spesifik jika deploy nanti

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];

