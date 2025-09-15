<?php

// config/database.php

return [
    'driver' => $_ENV['DB_CONNECTION'] ?? 'mysql',

    'mysql' => [
        'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
        'port' => $_ENV['DB_PORT'] ?? '3306',
        'database' => $_ENV['DB_DATABASE'] ?? '',
        'username' => $_ENV['DB_USERNAME'] ?? 'root',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],

    // DSN string yang siap digunakan untuk koneksi PDO
    'dsn' => sprintf(
        '%s:host=%s;port=%s;dbname=%s;charset=%s',
        $_ENV['DB_CONNECTION'] ?? 'mysql',
        $_ENV['DB_HOST'] ?? '127.0.0.1',
        $_ENV['DB_PORT'] ?? '3306',
        $_ENV['DB_DATABASE'] ?? '',
        'utf8mb4'
    )
];
