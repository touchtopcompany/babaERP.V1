<?php

return [
    'driver' => 'mysql',
    'host' => config('subdomain.host'),
    'port' => config('subdomain.port'),
    'database' => config('subdomain.database'),
    'username' => config('subdomain.username'),
    'password' => config('subdomain.password'),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => null,
    'prefix_indexes' => true,
    'strict' => true,
    'engine' => 'InnoDB'
];