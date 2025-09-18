<?php

namespace TokoBot\Commands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Request;
use TokoBot\Models\UserModel as AppUserModel;
use TokoBot\Models\UserStateModel as AppUserStateModel;

class JualCommand extends UserCommand
{
    protected $name = 'jual';
    protected $description = 'Jual media';
    protected $usage = '/jual';
    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        $message = $this->getMessage();
        $user = $message->getFrom();
        $chatId = $message->getChat()->getId();
        $reply = $message->getReplyToMessage();

        // @phpstan-ignore-next-line
        if (!$reply || !$this->isValidMedia($reply)) {
            return Request::sendMessage(['chat_id' => $chatId, 'text' => '❌ Error: Perintah ini harus digunakan dengan me-reply media (foto, video, dokumen, atau audio).']);
        }

        $sellerResult = AppUserModel::findOrCreateSellerId($user->getId());
        if ($sellerResult['created']) {
            $newId = $sellerResult['id'];
            Request::sendMessage(['chat_id' => $chatId, 'text' => "🎉 Selamat! Anda sekarang adalah penjual. ID Penjual Anda: `{$newId}`. Gunakan ID ini untuk mengelola penjualan Anda."]);
        }

        $state = AppUserStateModel::findByTelegramId($user->getId());

        $context = $state ? json_decode($state['context'], true) : ['items' => []];
        
        $newItem = [
            'message_id' => $reply->getMessageId(),
            'chat_id' => $reply->getChat()->getId(),
            'media_group_id' => $reply->getMediaGroupId(),
            'raw_media' => $reply->getRawData()
        ];
        $context['items'][] = $newItem;

        AppUserStateModel::updateState($user->getId(), \TokoBot\Helpers\BotState::SELLING_BATCHING_ITEMS, $context);

        $itemCount = count($context['items']);
        $groupCount = count(array_unique(array_column($context['items'], 'media_group_id')));

        $responseText = "✅ Item ditambahkan.\nTotal: {$itemCount} item (dalam {$groupCount} grup).\n\nLanjutkan /jual pada media lain, atau kirim HARGA untuk selesai, atau /cancel untuk batal.";
        return Request::sendMessage(['chat_id' => $chatId, 'text' => $responseText]);
    }

    private function isValidMedia(Message $message): bool
    {
        // @phpstan-ignore-next-line
        return $message->getPhoto() !== null || $message->getVideo() !== null || $message->getDocument() !== null || $message->getAudio() !== null;
    }
}