<?php

namespace TokoBot\Commands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use TokoBot\Helpers\Database;

class CancelCommand extends UserCommand
{
    protected $name = 'cancel';
    protected $description = 'Batalkan proses yang sedang berjalan';
    protected $usage = '/cancel';
    protected $version = '1.0.0';
    protected $private_only = true;

    public function execute(): ServerResponse
    {
        $message = $this->getMessage();
        $chatId = $message->getChat()->getId();
        $userId = $message->getFrom()->getId();

        $pdo = Database::getInstance();

        $stmt = $pdo->prepare("SELECT * FROM user_states WHERE telegram_id = ?");
        $stmt->execute([$userId]);
        $state = $stmt->fetch();

        if (!$state) {
            return Request::sendMessage(['chat_id' => $chatId, 'text' => 'Tidak ada proses yang sedang berjalan untuk dibatalkan.']);
        }

        // Clear user state
        $this->clearUserState($userId, $pdo);

        return Request::sendMessage(['chat_id' => $chatId, 'text' => '✅ Proses berhasil dibatalkan.']);
    }

    private function clearUserState(int $userId, \PDO $pdo): void
    {
        $stmt = $pdo->prepare("DELETE FROM user_states WHERE telegram_id = ?");
        $stmt->execute([$userId]);
    }
}