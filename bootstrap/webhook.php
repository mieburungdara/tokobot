<?php

// This is a shared bootstrap file for all standalone webhook entry points.
// It sets up a minimal application environment to access helpers and configurations.

// Prevent direct script access.
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(403);
    die('Forbidden');
}

// Define ROOT_PATH, the project root directory, if not already defined.
// This is essential for locating other files like .env and the vendor directory.
if (!defined('ROOT_PATH')) {
    // Assumes this file is in /bootstrap/
    define('ROOT_PATH', dirname(__DIR__));
}

// Include the Composer autoloader.
require_once ROOT_PATH . '/vendor/autoload.php';

// Load environment variables from the .env file.
// This is crucial for database connections, API keys, and other configurations.
if (class_exists('Dotenv\Dotenv')) {
    try {
        $dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
        $dotenv->load();
    } catch (\Dotenv\Exception\InvalidPathException $e) {
        // It's fine if the .env file is missing, but log it for debugging.
        // The application might be configured via server environment variables instead.
        error_log('Could not find .env file for webhook: ' . $e->getMessage());
    }
}

// Define other path constants for compatibility if they are not already set.
if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', ROOT_PATH . '/config');
}

// Register a basic exception handler for this entry point to ensure errors are logged.
set_exception_handler(function ($exception) {
    // Use the application's logger if it's available.
    if (class_exists('\TokoBot\Helpers\Logger')) {
        \TokoBot\Helpers\Logger::channel('critical')->critical(
            'Unhandled exception in webhook: ' . $exception->getMessage(),
            [
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]
        );
    } else {
        // Fallback to PHP's error log if the logger can't be loaded.
        error_log('Unhandled exception in webhook: ' . $exception->getMessage() . "\n" . $exception->getTraceAsString());
    }

    // Avoid leaking exception details to the public.
    if (!headers_sent()) {
        http_response_code(500);
        echo 'Internal Server Error';
    }
});
