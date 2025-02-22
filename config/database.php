<?php

return [
    'driver' => env('DATABASE_DRIVER', 'mysql'),
    'host' => env('DATABASE_HOST'),
    'database' => env('DATABASE_NAME'),
    'username' => env('DATABASE_USERNAME'),
    'password' => env('DATABASE_PASSWORD'),
    'charset' => env('DATABASE_CHARSET', 'utf8mb4'),
    'collation' => env('DATABASE_COLLATION', 'utf8mb4_unicode_ci'),
    'prefix' => env('DATABASE_PREFIX'),
];
