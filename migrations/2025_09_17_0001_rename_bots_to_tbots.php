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

echo "Running migration: Rename bots to tbots...\n";

try {
    $pdo = Database::getInstance();
    echo "Database connection successful.\n";

    // SQL to rename table
    $sql = "RENAME TABLE `bots` TO `tbots`;";

    echo "Executing RENAME TABLE query...\n";
    $pdo->exec($sql);
    echo "SUCCESS: Table 'bots' renamed to 'tbots'.\n";

} catch (PDOException $e) {
    die("ERROR: Could not run migration. " . $e->getMessage() . "\n");
} catch (Exception $e) {
    die("An unexpected error occurred: " . $e->getMessage() . "\n");
}