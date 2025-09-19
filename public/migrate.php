<?php

// A one-time, secure script to run a database migration via web request.

// --- CONFIGURATION ---
// This token prevents unauthorized execution. It must be passed as a query parameter.
$secretToken = 'a8f5b1e3c9d7a2b6e8f1c5d9a7b2e3f4'; // This is a hardcoded, one-time token.

// --- SECURITY CHECK ---
if (!isset($_GET['token']) || !hash_equals($secretToken, $_GET['token'])) {
    http_response_code(403);
    die('ERROR: Invalid or missing token.');
}

// --- BOOTSTRAP APPLICATION ---
// This is necessary to get database credentials and autoloading.
require_once __DIR__ . '/../bootstrap/app.php';

header('Content-Type: text/plain');

echo "Migration Script Started...\n";

try {
    // --- GET DATABASE CONNECTION ---
    $pdo = TokoBot\Helpers\Database::getInstance();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful.\n";

    // --- MIGRATION LOGIC ---
    $tableName = 'users';
    $sql = "
        ALTER TABLE `{$tableName}` 
        ADD COLUMN `remember_selector` VARCHAR(255) NULL DEFAULT NULL AFTER `password`,
        ADD COLUMN `remember_validator_hash` VARCHAR(255) NULL DEFAULT NULL AFTER `remember_selector`,
        ADD UNIQUE INDEX `remember_selector_unique` (`remember_selector`);
    ";

    echo "Executing ALTER TABLE query...\n";
    $pdo->exec($sql);
    echo "Query executed successfully. Table '{$tableName}' has been modified.\n";

    // --- SUCCESS ---
    echo "\nMigration completed successfully!\n";

} catch (PDOException $e) {
    // --- ERROR HANDLING ---
    http_response_code(500);
    echo "\n!!! MIGRATION FAILED !!!\n";
    echo "Error: " . $e->getMessage() . "\n";
    // Do not delete the script on failure, so it can be debugged or retried.
    exit();
}

// --- SELF-DESTRUCT ---
// For security, the script deletes itself after a successful run.
if (unlink(__FILE__)) {
    echo "\nScript has been successfully self-deleted.";
} else {
    echo "\nWarning: Could not self-delete script. Please remove public/migrate.php manually!";
}