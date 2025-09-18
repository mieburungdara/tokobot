<?php

// public/migrate.php

// WARNING: This script is for demonstration and emergency use only.
// It performs direct database schema and data manipulation.
// In a production environment, this script should be heavily secured (e.g., IP whitelist, strong authentication)
// or removed after use.

// Set error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define ROOT_PATH if not already defined (for standalone execution)
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

require_once ROOT_PATH . '/vendor/autoload.php';
require_once ROOT_PATH . '/bootstrap/app.php'; // Include bootstrap/app.php for constants

use TokoBot\Helpers\Database;

header('Content-Type: text/plain');

echo "Starting custom database migration...\n\n";

try {
    $pdo = Database::getInstance();
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    // --- Migration 1: Create roles table ---
    echo "Attempting to create 'roles' table...\n";
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS `roles` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(50) NOT NULL UNIQUE,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "'roles' table created or already exists.\n\n";

    // --- Migration 2: Add role_id to users table and set foreign key ---
    echo "Attempting to add 'role_id' to 'users' table...\n";
    // Check if column exists before adding
    $columns = $pdo->query("SHOW COLUMNS FROM `users` LIKE 'role_id'")->fetchAll();
    if (empty($columns)) {
        $pdo->exec("ALTER TABLE `users` ADD COLUMN `role_id` INT(11) NULL AFTER `telegram_id`;");
        echo "'role_id' column added to 'users' table.\n";
    } else {
        echo "'role_id' column already exists in 'users' table.\n";
    }

    // Add foreign key constraint (only if role_id was just added or not already constrained)
    // This part is tricky with direct SQL, Phinx handles this better.
    // We'll try to add it, but it might fail if already exists or if data is inconsistent.
    echo "Attempting to add foreign key constraint to 'users.role_id'...\n";
    try {
        $pdo->exec("ALTER TABLE `users` ADD CONSTRAINT `fk_users_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;");
        echo "Foreign key constraint added.\n\n";
    } catch (\PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate foreign key constraint name') !== false || strpos($e->getMessage(), 'Foreign key constraint already exists') !== false) {
            echo "Foreign key constraint already exists.\n\n";
        } else {
            throw $e; // Re-throw other unexpected errors
        }
    }

    // --- Seeder: Populate roles table ---
    echo "Attempting to seed 'roles' table...\n";
    $rolesToInsert = [
        ['name' => 'admin'],
        ['name' => 'member'],
        ['name' => 'seller'],
    ];

    foreach ($rolesToInsert as $roleData) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO `roles` (`name`, `created_at`, `updated_at`) VALUES (?, NOW(), NOW())");
        $stmt->execute([$roleData['name']]);
        if ($stmt->rowCount() > 0) {
            echo "Role '{$roleData['name']}' inserted.\n";
        } else {
            echo "Role '{$roleData['name']}' already exists.\n";
        }
    }
    echo "Roles seeding complete.\n\n";

    // --- Data Migration: Update existing users with a default role_id ---
    echo "Attempting to update existing users with default role...\n";
    // Get the ID of the 'member' role
    $memberRoleId = $pdo->query("SELECT id FROM `roles` WHERE `name` = 'member'")->fetchColumn();

    if ($memberRoleId) {
        // Update users who currently have role_id as NULL or 0 (if any)
        // and also update users whose 'role' column (string) matches a role name
        $pdo->exec("UPDATE `users` SET `role_id` = {$memberRoleId} WHERE `role_id` IS NULL OR `role_id` = 0;");
        echo "Users with NULL/0 role_id updated to 'member'.\n";

        // Optionally, if there's a 'role' string column, map it
        // This assumes 'role' column exists and contains string names like 'admin', 'member', 'seller'
        $adminRoleId = $pdo->query("SELECT id FROM `roles` WHERE `name` = 'admin'")->fetchColumn();
        if ($adminRoleId) {
            $pdo->exec("UPDATE `users` SET `role_id` = {$adminRoleId} WHERE `role` = 'admin' AND (`role_id` IS NULL OR `role_id` = {$memberRoleId});");
            echo "Users with string role 'admin' updated to admin role_id.\n";
        }

    } else {
        echo "'member' role not found in roles table. Skipping default role assignment for users.\n";
    }
    echo "User role assignment complete.\n\n";

    echo "Custom migration finished successfully!\n";

} catch (\PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
    echo "SQLSTATE: " . $e->getCode() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
} catch (\Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}


