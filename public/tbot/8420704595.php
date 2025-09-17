<?php
require_once __DIR__ . '/../../vendor/autoload.php';

// Define CONFIG_PATH if not already defined (e.g., in bootstrap)
if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', __DIR__ . '/../../config');
}

// Load bot tokens
$botTokens = require CONFIG_PATH . '/tbots.php';

// Bot ID for this entry point
$botId = 8420704595;
// Bot ID untuk entry point ini didapatkan secara dinamis dari nama file.
$botId = (int) basename(__FILE__, '.php');

// Check if bot token exists for this ID
if (!isset($botTokens[$botId])) {
    // Log error or return an error response if the bot token is not found
    // For now, we'll just exit to prevent further errors.
    http_response_code(404);
    echo "Bot configuration not found for ID: " . $botId;
    exit();
}

// Prepare bot configuration for GenericBotHandler
$botConfig = [
    'id' => $botId,
    'token' => $botTokens[$botId],
    // Add other necessary bot configurations here if needed by GenericBotHandler
];

// Entry point for bot ID: 8420704595
(new \TokoBot\BotHandlers\GenericBotHandler($botConfig))->handle();