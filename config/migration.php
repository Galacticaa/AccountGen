<?php

$config = include 'config/database.php';

return [
    'paths' => [
        'migrations' => 'src/Database/Migrations',
        'seeds' => 'src/Database/Seeds',
    ],
    'environments' => [
        'default_migration_table' => 'migrations',
        'default_database' => 'default',
        'default' => [
            'adapter' => 'mysql',
            'charset' => $config['charset'],
            'collation' => $config['collation'],
            'host' => $config['host'],
            'name' => $config['database'],
            'user' => $config['username'],
            'pass' => $config['password'],
            'port' => $config['port'],
        ]
    ]
];
