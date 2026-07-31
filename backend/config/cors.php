<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],

    // GUNAKAN BINTANG (*) DULU UNTUK MEMASTIKAN TIDAK ADA BLOKIR
    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,

    // WAJIB FALSE KARENA KITA PAKAI BEARER TOKEN DI LOCALSTORAGE
    'supports_credentials' => false,
];
