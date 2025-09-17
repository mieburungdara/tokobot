<?php

// config/database.php

// Initialize variables with default values
$db_connection = 'mysql';
$db_host = '127.0.0.1';
$db_port = '3306';
$db_database = '';
$db_username = 'root';
$db_password = '';

// --- Reliable PHP-based config loader ---
// This is the preferred method. It tries to load credentials from a git-ignored PHP file.
$localConfigFile = __DIR__ . '/local.php';
if (file_exists($localConfigFile)) {
    $localConfig = require $localConfigFile;
    if (isset($localConfig['database'])) {
        $db_host = $localConfig['database']['host'] ?? $db_host;
        $db_port = $localConfig['database']['port'] ?? $db_port;
        $db_database = $localConfig['database']['database'] ?? $db_database;
        $db_username = $localConfig['database']['username'] ?? $db_username;
        $db_password = $localConfig['database']['password'] ?? $db_password;
    }
} 
// --- Fallback to Environment Variables ---
// This method is used if the local.php file does not exist.
else {
    $db_connection = $_ENV['DB_CONNECTION'] ?? $db_connection;
    $db_host = $_ENV['DB_HOST'] ?? $db_host;
    $db_port = $_ENV['DB_PORT'] ?? $db_port;
    $db_database = $_ENV['DB_DATABASE'] ?? $db_database;
    $db_username = $_ENV['DB_USERNAME'] ?? $db_username;
    $db_password = $_ENV['DB_PASSWORD'] ?? $db_password;
}

return [
    'driver' => $db_connection,

    'mysql' => [
        'host' => $db_host,
        'port' => $db_port,
        'database' => $db_database,
        'username' => $db_username,
        'password' => $db_password,
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],

    // DSN string built from the determined configuration
    'dsn' => sprintf(
        '%s:host=%s;port=%s;dbname=%s;charset=%s',
        $db_connection,
        $db_host,
        $db_port,
        $db_database,
        'utf8mb4'
    )
];