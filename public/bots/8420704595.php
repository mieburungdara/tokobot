<?php
require_once __DIR__ . '/../../vendor/autoload.php';

// Entry point for bot ID: 8420704595
$botConfig = [\'id\' => 8420704595];

(new \TokoBot\BotHandlers\GenericBotHandler($botConfig))->handle();
