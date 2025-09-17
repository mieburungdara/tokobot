<?php

namespace TokoBot\Controllers;

use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Request;

class BotController
{
    protected Telegram $telegram;

    public function __construct(string $token)
    {
        $this->telegram = new Telegram($token, 'TokoBot');
    }

    public function handle()
    {
        // This method is likely unused in the new webhook flow.
        // Kept for potential future use or manual polling.
        $response = Request::getUpdates([]);
        if ($response->isOk()) {
            $updates = $response->getResult();

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
}