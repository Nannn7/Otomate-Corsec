<?php

return [
    'oracle' => [
            'driver'        => 'oracle',
            'host'          => env('DB_ORACLE_HOST', '10.0.20.161'),
            'port'          => env('DB_ORACLE_PORT', '1521'),
            'database'      => env('DB_ORACLE_DATABASE', 'IST77'),
            'username'      => env('DB_ORACLE_USERNAME', 'support'),
            'password'      => env('DB_ORACLE_PASSWORD', 'support'),
            'service_name'  => env('DB_ORACLE_SERVICE_NAME', 'oasis'),
            'charset'       => env('DB_ORACLE_CHARSET', 'AL32UTF8'),
            'prefix'        => env('DB_ORACLE_PREFIX', ''),
        ],
];
