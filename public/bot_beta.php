<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$bots = require __DIR__ . '/../config/bots.php';
$botConfig = $bots['beta'];

$handlerClass = $botConfig['handler'];
$bot = new $handlerClass($botConfig['token']);

$bot->handle();
