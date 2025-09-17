<?php

namespace TokoBot\Commands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use TokoBot\Helpers\Database;

class CancelCommand extends UserCommand
{
    protected $name = 'cancel';
    protected $description = 'Cancel the current operation';
    protected $usage = '/cancel';
    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        $message = $this->getMessage();
        $userId = $message->getFrom()->getId();
        $chatId = $message->getChat()->getId();

        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM user_states WHERE telegram_id = ?");
        $stmt->execute([$userId]);
        $state = $stmt->fetch();

        if ($state) {
            $deleteStmt = $pdo->prepare("DELETE FROM user_states WHERE telegram_id = ?");
            $deleteStmt->execute([$userId]);
            $responseText = 'Proses sebelumnya telah dibatalkan.';
        } else {
            $responseText = 'Tidak ada proses yang sedang berjalan untuk dibatalkan.';
        }

        return Request::sendMessage([
            'chat_id' => $chatId,
            'text'    => $responseText,
        ]);
    }
}
