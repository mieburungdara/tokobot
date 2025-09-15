<?php

namespace TokoBot\Controllers;

use Telegram\Bot\Api;

class BotController
{
    protected Api $telegram;

    public function __construct(string $token)
    {
        $this->telegram = new Api($token);
    }

    public function handle()
    {
        $updates = $this->telegram->getUpdates();
        // Simple echo bot for now
        foreach ($updates as $update) {
            if ($update->getMessage()) {
                $this->telegram->sendMessage([
                    'chat_id' => $update->getMessage()->getChat()->getId(),
                    'text' => 'Echo: ' . $update->getMessage()->getText(),
                ]);
            }
        }
    }
}
