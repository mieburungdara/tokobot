<?php

namespace TokoBot\Controllers;

use TelegramBot\Telegram;
use TelegramBot\Request; // Add this

class BotController
{
    public function __construct(string $token)
    {
        // The constructor is still needed to set the token globally for the library
        new Telegram($token);
    }

    public function handle()
    {
        // API calls are made statically via the Request class
        $response = Request::getUpdates([]);
        $updates = $response->getResult();

        // Simple echo bot for now
        foreach ($updates as $update) {
            if ($update->getMessage()) {
                Request::sendMessage([
                    'chat_id' => $update->getMessage()->getChat()->getId(),
                    'text' => 'Echo: ' . $update->getMessage()->getText(),
                ]);
            }
        }
    }
}