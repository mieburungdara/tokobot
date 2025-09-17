<?php

// Load Composer autoloader
require_once 'vendor/autoload.php';

// --- Reliable Config Loading for Phinx ---

// Initialize variables with default values
$db_host = '127.0.0.1';
$db_port = '3306';
$db_database = '';
$db_username = 'root';
$db_password = '';

// 1. Attempt to load from local.php (most reliable)
$localConfigFile = __DIR__ . '/config/local.php';
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
// 2. Fallback to .env file (less reliable in some environments)
else if (class_exists('Dotenv\Dotenv')) {
    try {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        $db_host = $_ENV['DB_HOST'] ?? $db_host;
        $db_port = $_ENV['DB_PORT'] ?? $db_port;
        $db_database = $_ENV['DB_DATABASE'] ?? $db_database;
        $db_username = $_ENV['DB_USERNAME'] ?? $db_username;
        $db_password = $_ENV['DB_PASSWORD'] ?? $db_password;
    } catch (\Exception $e) {
        // Do nothing, defaults will be used
    }
}

return
[
    'paths' => [
        'migrations' => __DIR__ . '/db/migrations',
        'seeds' => __DIR__ . '/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'adapter' => 'mysql',
            'host' => $db_host,
            'name' => $db_database,
            'user' => $db_username,
            'pass' => $db_password,
            'port' => $db_port,
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];