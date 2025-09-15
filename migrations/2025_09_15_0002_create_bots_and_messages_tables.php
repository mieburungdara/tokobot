<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Define constants
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

use TokoBot\Helpers\Database;

echo "Running migration: Create Messages and Bots Tables...\n";

try {
    $pdo = Database::getInstance();
    echo "Database connection successful.\n";

    // SQL for bots table
    $sql_bots = "
    CREATE TABLE IF NOT EXISTS `bots` (
      `id` BIGINT NOT NULL COMMENT 'Bot User ID from Telegram',
      `username` VARCHAR(255) NULL,
      `first_name` VARCHAR(255) NOT NULL,
      `is_bot` TINYINT(1) NOT NULL DEFAULT 1,
      `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    // SQL for messages table
    $sql_messages = "
    CREATE TABLE IF NOT EXISTS `messages` (
      `id` BIGINT NOT NULL COMMENT 'Update ID from Telegram',
      `message_id` BIGINT NOT NULL COMMENT 'Message ID from Telegram',
      `user_id` BIGINT NOT NULL COMMENT 'Corresponds to users.telegram_id',
      `chat_id` BIGINT NOT NULL,
      `bot_id` BIGINT NOT NULL COMMENT 'Corresponds to bots.id',
      `text` TEXT NULL,
      `raw_update` JSON NOT NULL,
      `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      INDEX `user_id_idx` (`user_id`),
      INDEX `bot_id_idx` (`bot_id`),
      INDEX `chat_id_idx` (`chat_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    echo "Executing CREATE TABLE query for 'bots'வைக்...
";
    $pdo->exec($sql_bots);
    echo "SUCCESS: 'bots' table created or already exists.\n";

    echo "Executing CREATE TABLE query for 'messages'...
";
    $pdo->exec($sql_messages);
    echo "SUCCESS: 'messages' table created or already exists.\n";

} catch (\PDOException $e) {
    die("ERROR: Could not run migration. " . $e->getMessage() . "\n");
} catch (\Exception $e) {
    die("An unexpected error occurred: " . $e->getMessage() . "\n");
}

