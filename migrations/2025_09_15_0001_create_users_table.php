<?php

// migrations/YYYY_MM_DD_0001_create_users_table.php
// Script untuk membuat tabel users.

require_once __DIR__ . '/../vendor/autoload.php';

// Define constants
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

use TokoBot\Helpers\Database;

echo "Running migration: Create Users Table...\n";

try {
    $pdo = Database::getInstance();
    echo "Database connection successful.\n";

    $sql = "
    CREATE TABLE IF NOT EXISTS `users` (
      `telegram_id` BIGINT NOT NULL,
      `username` VARCHAR(255) NULL,
      `first_name` VARCHAR(255) NOT NULL,
      `last_name` VARCHAR(255) NULL,
      `role` VARCHAR(50) NOT NULL DEFAULT 'member',
      `login_token` VARCHAR(255) NULL,
      `token_expires_at` DATETIME NULL,
      `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`telegram_id`),
      UNIQUE KEY `login_token_unique` (`login_token`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    echo "Executing CREATE TABLE query...\n";
    $pdo->exec($sql);
    echo "SUCCESS: 'users' table created or already exists.\n";

} catch (PDOException $e) {
    die("ERROR: Could not run migration. " . $e->getMessage() . "\n");
} catch (Exception $e) {
    die("An unexpected error occurred: " . $e->getMessage() . "\n");
}
