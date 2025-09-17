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

echo "Running migration: Add token to tbots table...\n";

try {
    $pdo = Database::getInstance();
    echo "Database connection successful.\n";

    // SQL to add token column
    $sql = "ALTER TABLE `tbots` ADD `token` VARCHAR(255) NULL AFTER `is_bot`;";

    echo "Executing ALTER TABLE query...\n";
    $pdo->exec($sql);
    echo "SUCCESS: Column 'token' added to 'tbots' table.\n";

} catch (PDOException $e) {
    die("ERROR: Could not run migration. " . $e->getMessage() . "\n");
} catch (Exception $e) {
    die("An unexpected error occurred: " . $e->getMessage() . "\n");
}