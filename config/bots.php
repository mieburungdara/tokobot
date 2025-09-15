<?php

return [
    'alpha' => [
        'token' => $_ENV['BOT_ALPHA_TOKEN'],
        'handler' => TokoBot\Controllers\BotController::class,
    ],
    'beta' => [
        'token' => $_ENV['BOT_BETA_TOKEN'],
        'handler' => TokoBot\Controllers\BotController::class,
    ],
];
