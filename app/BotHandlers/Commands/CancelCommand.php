<?php

namespace TokoBot\Commands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use TokoBot\Models\UserStateModel;

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

        $state = UserStateModel::findByTelegramId($userId);

        if (!$state || $state['state'] === \TokoBot\Helpers\BotState::IDLE) {
            return Request::sendMessage(['chat_id' => $chatId, 'text' => 'Tidak ada proses yang sedang berjalan untuk dibatalkan.']);
        }

        // Clear user state
        UserStateModel::clearState($userId);

        return Request::sendMessage(['chat_id' => $chatId, 'text' => '✅ Proses berhasil dibatalkan.']);
    }
}