<?php

namespace TokoBot\BotHandlers\Commands;

use TelegramBot\Entities\Update;
use TelegramBot\Entities\Message;
use TelegramBot\Request;
use TokoBot\Helpers\Database;
use TokoBot\Helpers\Logger;

class JualCommand implements CommandInterface
{
    protected int $botId;
    protected ?string $botToken;
    protected \PDO $pdo;

    public function __construct(int $botId, ?string $botToken)
    {
        $this->botId = $botId;
        $this->botToken = $botToken;
        $this->pdo = Database::getInstance();
    }

    public function handle(Update $update, array $args = []): void
    {
        $message = $update->getMessage();
        $user = $message->getFrom();
        $chatId = $message->getChat()->getId();

        $reply = $message->getReplyToMessage();

        // 1. Validate that it's a reply to a valid media
        if (!$reply || !$this->isValidMedia($reply)) {
            Request::sendMessage(['chat_id' => $chatId, 'text' => '❌ Error: Perintah ini harus digunakan dengan me-reply media (foto, video, dokumen, atau audio).']);
            return;
        }

        // 2. Get or create seller_id
        $sellerId = $this->getOrCreateSellerId($user->getId());

        // 3. Get current state or create a new one
        $stmt = $this->pdo->prepare("SELECT * FROM user_states WHERE telegram_id = ?");
        $stmt->execute([$user->getId()]);
        $state = $stmt->fetch();

        $context = $state ? json_decode($state['context'], true) : ['items' => []];
        
        // 4. Add the new item to the context
        $newItem = [
            'message_id' => $reply->getMessageId(),
            'chat_id' => $reply->getChat()->getId(),
            'media_group_id' => $reply->getMediaGroupId(),
            'raw_media' => json_encode($reply) // Store the whole message object
        ];
        $context['items'][] = $newItem;

        // 5. Upsert state in DB
        $sql = "INSERT INTO user_states (telegram_id, state, context) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE state = VALUES(state), context = VALUES(context)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user->getId(), 'selling_batching_items', json_encode($context)]);

        // 6. Send feedback to user
        $itemCount = count($context['items']);
        $groupCount = count(array_unique(array_column($context['items'], 'media_group_id')));

        $responseText = "✅ Item ditambahkan.\nTotal: {$itemCount} item (dalam {$groupCount} grup).\n\nLanjutkan /jual pada media lain, atau kirim HARGA untuk selesai, atau /cancel untuk batal.";
        Request::sendMessage(['chat_id' => $chatId, 'text' => $responseText]);
    }

    private function isValidMedia(Message $message): bool
    {
        return $message->getPhoto() !== null || $message->getVideo() !== null || $message->getDocument() !== null || $message->getAudio() !== null;
    }

    private function getOrCreateSellerId(int $userId): string
    {
        $stmt = $this->pdo->prepare("SELECT seller_id FROM users WHERE telegram_id = ?");
        $stmt->execute([$userId]);
        $sellerId = $stmt->fetchColumn();

        if ($sellerId) {
            return $sellerId;
        }

        // Create new one
        do {
            $newId = strtoupper(substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ', 5)), 0, 5));
            $checkStmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE seller_id = ?");
            $checkStmt->execute([$newId]);
        } while ($checkStmt->fetchColumn() > 0);

        $updateStmt = $this->pdo->prepare("UPDATE users SET seller_id = ? WHERE telegram_id = ?");
        $updateStmt->execute([$newId, $userId]);

        return $newId;
    }
}