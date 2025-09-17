<?php

namespace TokoBot\BotHandlers\Commands;

use TelegramBot\Entities\Update;
use TelegramBot\Telegram;
use TelegramBot\Request;

class StartCommand implements CommandInterface
{
    protected ?string $botToken;

    public function __construct(int $botId, ?string $botToken)
    {
        $this->botToken = $botToken;
    }

    public function handle(Update $update, array $args = []): void
    {
        $user = $update->getMessage()->getFrom();
        $chatId = $update->getMessage()->getChat()->getId();

        $text = "Selamat datang, " . $user->getFirstName() . "!\n\n";
        $text .= "Saya adalah bot yang siap membantu Anda. Silakan gunakan perintah /login untuk masuk ke sistem.";

        if ($this->botToken) {
            new Telegram($this->botToken);
            Request::sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
            ]);
        }
    }
}
