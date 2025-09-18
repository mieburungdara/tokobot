<?php

namespace TokoBot\Commands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Request;
use TokoBot\Helpers\Database;

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
        $pdo = Database::getInstance();

        // @phpstan-ignore-next-line
        if (!$reply || !$this->isValidMedia($reply)) {
            return Request::sendMessage(['chat_id' => $chatId, 'text' => '❌ Error: Perintah ini harus digunakan dengan me-reply media (foto, video, dokumen, atau audio).']);
        }

        $sellerId = $this->getOrCreateSellerId($user->getId(), $pdo, $chatId);

        $stmt = $pdo->prepare("SELECT * FROM user_states WHERE telegram_id = ?");
        $stmt->execute([$user->getId()]);
        $state = $stmt->fetch();

        $context = $state ? json_decode($state['context'], true) : ['items' => []];
        
        $newItem = [
            'message_id' => $reply->getMessageId(),
            'chat_id' => $reply->getChat()->getId(),
            'media_group_id' => $reply->getMediaGroupId(),
            'raw_media' => $reply->getRawData()
        ];
        $context['items'][] = $newItem;

        $sql = "INSERT INTO user_states (telegram_id, state, context) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE state = VALUES(state), context = VALUES(context)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user->getId(), 'selling_batching_items', json_encode($context)]);

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

    private function getOrCreateSellerId(int $userId, \PDO $pdo, int $chatId): string
    {
        $stmt = $pdo->prepare("SELECT seller_id FROM users WHERE telegram_id = ?");
        $stmt->execute([$userId]);
        $sellerId = $stmt->fetchColumn();

        if ($sellerId) {
            return $sellerId;
        }

        do {
            $newId = strtoupper(substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ', 5)), 0, 5));
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE seller_id = ?");
            $checkStmt->execute([$newId]);
        } while ($checkStmt->fetchColumn() > 0);

        $updateStmt = $pdo->prepare("UPDATE users SET seller_id = ? WHERE telegram_id = ?");
        $updateStmt->execute([$newId, $userId]);

        Request::sendMessage(['chat_id' => $chatId, 'text' => "🎉 Selamat! Anda sekarang adalah penjual. ID Penjual Anda: `{$newId}`. Gunakan ID ini untuk mengelola penjualan Anda."]);

        return $newId;
    }
}
