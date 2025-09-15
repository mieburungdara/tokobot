<?php

require_once __DIR__ . '/vendor/autoload.php';

use Telegram\Bot\Api;

$telegram = new Api('YOUR_BOT_API_TOKEN');

$updates = $telegram->getUpdates();

print_r($updates);
