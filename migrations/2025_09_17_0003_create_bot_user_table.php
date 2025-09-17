<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use TokoBot\Helpers\Database;

// Define constants
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');

// Load .env
$dotenv = Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

echo "Running migration: Create bot_user table...\n";

try {
    $pdo = Database::getInstance();
    echo "Database connection successful.\n";

    // SQL to create bot_user table
    $sql = "
    CREATE TABLE IF NOT EXISTS `bot_user` (
      `bot_id` BIGINT NOT NULL,
      `user_id` BIGINT NOT NULL,
      `allows_write_to_pm` TINYINT(1) NOT NULL DEFAULT 0,
      `last_accessed_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`bot_id`, `user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    echo "Executing CREATE TABLE query for 'bot_user'...\n";
    $pdo->exec($sql);
    echo "SUCCESS: 'bot_user' table created or already exists.\n";

} catch (PDOException $e) {
    die("ERROR: Could not run migration. " . $e->getMessage() . "\n");
} catch (Exception $e) {
    die("An unexpected error occurred: " . $e->getMessage() . "\n");
}

