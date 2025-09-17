<?php

namespace TokoBot\BotHandlers;

use TokoBot\Helpers\Database;
use TokoBot\Helpers\Logger;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Entities\User;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\CallbackQuery;
use Longman\TelegramBot\Exception\TelegramException;

class GenericBotHandler
{
    protected int $botId;
    protected Telegram $telegram;
    protected ?\PDO $pdo;

    public function __construct(int $botId, Telegram $telegram)
    {
        $this->botId = $botId;
        $this->telegram = $telegram;
        $this->pdo = Database::getInstance();
    }

    public function handle(): ?ServerResponse
    {
        $update = new Update(json_decode($this->telegram->getCustomInput(), true), $this->telegram->getBotUsername());
        Logger::channel('telegram_raw')->info('Incoming Update', ['data' => $update->getRawData()]);

        $updateType = $update->getUpdateType();
        $user = null;

        if ($updateType === 'message') {
            $user = $update->getMessage()->getFrom();
            $this->logMessage($update);
            if ($this->handleStatefulMessage($update->getMessage())) {
                return Request::emptyResponse(); // Handled statefully
            }
        } elseif ($updateType === 'callback_query') {
            $user = $update->getCallbackQuery()->getFrom();
            $this->handleCallbackQuery($update->getCallbackQuery());
            return Request::emptyResponse(); // Handled callback query
        }

        if (!$user) {
            return Request::emptyResponse(); // No user, return empty response
        }

        $this->syncUser($user);

        // If not handled statefully or as a callback query, proceed to command handling
        $this->telegram->addCommandsPath(ROOT_PATH . '/app/Commands');
        
        $response = $this->telegram->handle();

        if ($response instanceof ServerResponse) {
            return $response;
        }

        return Request::emptyResponse();
    }


    private function handleStatefulMessage(Message $message): bool
    {
        $userId = $message->getFrom()->getId();
        $text = $message->getText();

        $stmt = $this->pdo->prepare("SELECT * FROM user_states WHERE telegram_id = ?");
        $stmt->execute([$userId]);
        $state = $stmt->fetch();

        if (!$state || $state['state'] !== 'selling_batching_items') {
            return false;
        }

        if (is_numeric($text)) {
            $price = (float) $text;
            $context = json_decode($state['context'], true);
            $context['price'] = $price;

            $updateSql = "UPDATE user_states SET state = ?, context = ? WHERE telegram_id = ?";
            $this->pdo->prepare($updateSql)->execute(['selling_awaiting_confirmation', json_encode($context), $userId]);

            $itemCount = count($context['items']);
            $responseText = "Anda akan menjual paket berisi {$itemCount} item dengan harga {$price}. Lanjutkan?";
            Request::sendMessage([
                'chat_id' => $userId,
                'text' => $responseText,
                'reply_markup' => json_encode(['inline_keyboard' => [[['text' => '✅ Jual', 'callback_data' => 'jual_confirm'], ['text' => '❌ Batal', 'callback_data' => 'jual_cancel']]]])
            ]);
            return true; // Message was handled
        }
        
        // If the text is not numeric and not a command, we can ignore it or reply with a helper text.
        if (is_string($text) && strlen($text) > 0 && $text[0] !== '/') {
            Request::sendMessage(['chat_id' => $userId, 'text' => "Saya menunggu harga (angka) atau perintah /cancel."]);
            return true;
        }


        return false;
    }

    private function handleCallbackQuery(CallbackQuery $callbackQuery): void
    {
        $userId = $callbackQuery->getFrom()->getId();
        $message = $callbackQuery->getMessage();
        $callbackData = $callbackQuery->getData();

        Request::answerCallbackQuery(['callback_query_id' => $callbackQuery->getId()]);

        if ($callbackData === 'jual_cancel') {
            $this->pdo->prepare("DELETE FROM user_states WHERE telegram_id = ?")->execute([$userId]);
            Request::editMessageText([
                'chat_id' => $message->getChat()->getId(),
                'message_id' => $message->getMessageId(),
                'text' => '❌ Penjualan dibatalkan.',
                'reply_markup' => ''
            ]);
        } elseif ($callbackData === 'jual_confirm') {
            $stmt = $this->pdo->prepare("SELECT * FROM user_states WHERE telegram_id = ? AND state = 'selling_awaiting_confirmation'");
            $stmt->execute([$userId]);
            $state = $stmt->fetch();

            if ($state) {
                Request::editMessageText([
                    'chat_id' => $message->getChat()->getId(),
                    'message_id' => $message->getMessageId(),
                    'text' => '⏳ Memproses penjualan... mohon tunggu.',
                    'reply_markup' => ''
                ]);
                $this->finalizeSale($state);
            }
        }
    }

    private function finalizeSale(array $state): void
    {
        $context = json_decode($state['context'], true);
        $userId = $state['telegram_id'];
        
        $this->pdo->beginTransaction();
        try {
            $sellerId = $this->pdo->query("SELECT seller_id FROM users WHERE telegram_id = {$userId}")->fetchColumn();

            $channelStmt = $this->pdo->prepare("SELECT * FROM bot_storage_channels WHERE bot_id = ? ORDER BY last_used_at ASC LIMIT 1");
            $channelStmt->execute([$this->botId]);
            $storageChannel = $channelStmt->fetch();
            $storageChannelId = $storageChannel ? (int)$storageChannel['channel_id'] : -1002649138088;

            $copiedMessageIds = [];
            foreach ($context['items'] as $item) {
                $response = Request::copyMessage([
                    'chat_id'      => $storageChannelId,
                    'from_chat_id' => $item['chat_id'],
                    'message_id'   => $item['message_id'],
                ]);
                if ($response->isOk()) {
                    $copiedMessageIds[] = $response->getResult()->getMessageId();
                }
            }

            if (count($copiedMessageIds) !== count($context['items'])) {
                throw new \Exception('Failed to copy all messages.');
            }

            $countStmt = $this->pdo->prepare("SELECT COUNT(*) FROM contents WHERE seller_telegram_id = ?");
            $countStmt->execute([$userId]);
            $newCount = $countStmt->fetchColumn() + 1;
            $contentUid = $sellerId . '_' . str_pad((string)$newCount, 4, '0', STR_PAD_LEFT);

            $contentSql = "INSERT INTO contents (content_uid, seller_telegram_id, price, status, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
            $this->pdo->prepare($contentSql)->execute([$contentUid, $userId, $context['price'], 'available']);
            $contentId = $this->pdo->lastInsertId();

            $mediaSql = "INSERT INTO media (content_id, file_type, file_unique_id, file_size, width, height, duration, original_message_id, original_media_group_id, backup_channel_id, backup_message_id, raw_telegram_metadata) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $mediaStmt = $this->pdo->prepare($mediaSql);

            foreach ($context['items'] as $index => $item) {
                $raw = $item['raw_media'];
                $mediaType = array_keys(array_intersect_key($raw, array_flip(['photo', 'video', 'document', 'audio'])))[0] ?? 'unknown';
                $mediaData = $raw[$mediaType];
                $file = is_array($mediaData) ? end($mediaData) : $mediaData;

                $mediaStmt->execute([
                    $contentId, $mediaType, $file['file_unique_id'], $file['file_size'] ?? null, $file['width'] ?? null, $file['height'] ?? null, $file['duration'] ?? null,
                    $item['message_id'], $item['media_group_id'], $storageChannelId, $copiedMessageIds[$index], json_encode($raw)
                ]);
            }

            if ($storageChannel) {
                $this->pdo->prepare("UPDATE bot_storage_channels SET last_used_at = NOW() WHERE id = ?")->execute([$storageChannel['id']]);
            }

            $this->pdo->commit();

            Request::sendMessage(['chat_id' => $userId, 'text' => "✅ Penjualan berhasil disimpan!\nNomor Konten: {$contentUid}"]);
            $this->pdo->prepare("DELETE FROM user_states WHERE telegram_id = ?")->execute([$userId]);

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            Logger::channel('app')->error('Failed to finalize sale', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            Request::sendMessage(['chat_id' => $userId, 'text' => 'Terjadi kesalahan fatal saat menyimpan penjualan. Silakan coba lagi.']);
        }
    }

    private function syncUser(User $user): void
    {
        $sql = "INSERT INTO users (telegram_id, username, first_name, last_name, last_activity_at) VALUES (?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE username = VALUES(username), first_name = VALUES(first_name), last_name = VALUES(last_name), last_activity_at = NOW()";
        $this->pdo->prepare($sql)->execute([$user->getId(), $user->getUsername(), $user->getFirstName(), $user->getLastName()]);
    }

    private function logMessage(Update $update): void
    {
        $message = $update->getMessage();
        $sql = "INSERT INTO messages (id, message_id, user_id, chat_id, bot_id, text, raw_update) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE message_id = VALUES(message_id), user_id = VALUES(user_id), chat_id = VALUES(chat_id), bot_id = VALUES(bot_id), text = VALUES(text), raw_update = VALUES(raw_update)";
        $this->pdo->prepare($sql)->execute([$update->getUpdateId(), $message->getMessageId(), $message->getFrom()->getId(), $message->getChat()->getId(), $this->botId, $message->getText(), json_encode($update->getRawData())]);
    }
}