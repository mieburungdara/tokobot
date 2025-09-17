<?php

namespace TokoBot\BotHandlers\Commands;

use TelegramBot\Entities\Update;
use TelegramBot\Request;
use TokoBot\Helpers\Database;

class CancelCommand implements CommandInterface
{
    protected ?string $botToken;
    protected \PDO $pdo;

    public function __construct(?string $botToken)
    {
        $this->botToken = $botToken;
        $this->pdo = Database::getInstance();
    }

    public function handle(Update $update, array $args = []): void
    {
        $userId = $update->getMessage()->getFrom()->getId();
        $chatId = $update->getMessage()->getChat()->getId();

        $stmt = $this->pdo->prepare("SELECT * FROM user_states WHERE telegram_id = ?");
        $stmt->execute([$userId]);
        $state = $stmt->fetch();

        if ($state) {
            $deleteStmt = $this->pdo->prepare("DELETE FROM user_states WHERE telegram_id = ?");
            $deleteStmt->execute([$userId]);
            $responseText = 'Proses sebelumnya telah dibatalkan.';
        } else {
            $responseText = 'Tidak ada proses yang sedang berjalan untuk dibatalkan.';
        }

        if ($this->botToken) {
            Request::sendMessage([
                'chat_id' => $chatId,
                'text' => $responseText,
            ]);
        }
    }
}