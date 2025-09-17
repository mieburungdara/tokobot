<?php

namespace TokoBot\BotHandlers;

use TokoBot\BotHandlers\Commands\StartCommand;
use TokoBot\BotHandlers\Commands\JualCommand;
use TokoBot\BotHandlers\Commands\CancelCommand;
use TokoBot\Helpers\Database;
use TelegramBot\Telegram;
use TelegramBot\Request;
use TokoBot\Helpers\Logger;
use TelegramBot\Entities\Update;
use TelegramBot\Entities\User;
use TelegramBot\Entities\Message;
use TelegramBot\Entities\Chat;
use TelegramBot\Entities\CallbackQuery;

class GenericBotHandler
{
    protected int $botId;
    protected ?string $botToken;
    protected ?\PDO $pdo;

    protected array $commands = [
        '/start' => StartCommand::class,
        '/jual' => JualCommand::class,
        '/cancel' => CancelCommand::class,
    ];

    public function __construct(array $botConfig)
    {
        $this->botId = $botConfig['id'];
        $this->pdo = Database::getInstance();
        $this->botToken = \TokoBot\Models\Bot::findTokenById($this->botId);
    }

    public function handle()
    {
        try {
            $update = Telegram::getUpdate();
            Logger::channel('telegram_raw')->info('Incoming Update', ['data' => $update->getRawData()]);

            if ($update->getCallbackQuery()) {
                $this->handleCallbackQuery($update->getCallbackQuery());
                return;
            }

            if ($update->getMessage()) {
                $message = $update->getMessage();
                $user = $message->getFrom();
                
                $this->syncUser($user);
                $this->logMessage($update, $message, $user, $message->getChat(), $message->getText());

                if ($this->handleStatefulMessage($update)) {
                    return;
                }

                $this->dispatchCommand($update);
            }
        } catch (\Exception $e) {
            Logger::channel('app')->error("Bot Handler Error for Bot ID {$this->botId}", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function handleStatefulMessage(Update $update): bool
    {
        $userId = $update->getMessage()->getFrom()->getId();
        $text = $update->getMessage()->getText();

        $stmt = $this->pdo->prepare("SELECT * FROM user_states WHERE telegram_id = ?");
        $stmt->execute([$userId]);
        $state = $stmt->fetch();

        if (!$state) {
            return false;
        }

        if ($state['state'] === 'selling_batching_items') {
            if (is_numeric($text)) {
                $price = (float) $text;
                $context = json_decode($state['context'], true);
                $context['price'] = $price;

                $updateSql = "UPDATE user_states SET state = ?, context = ? WHERE telegram_id = ?";
                $updateStmt = $this->pdo->prepare($updateSql);
                $updateStmt->execute(['selling_awaiting_confirmation', json_encode($context), $userId]);

                $itemCount = count($context['items']);
                $responseText = "Anda akan menjual paket berisi {$itemCount} item dengan harga {$price}. Lanjutkan?";
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => '✅ Jual', 'callback_data' => 'jual_confirm'],
                            ['text' => '❌ Batal', 'callback_data' => 'jual_cancel'],
                        ]
                    ]
                ];

                Request::sendMessage([
                    'chat_id' => $userId,
                    'text' => $responseText,
                    'reply_markup' => json_encode($keyboard)
                ]);
            } else {
                // Not a price, so we assume it's not for the bot in this state
                // We let it fall through to the command dispatcher
                return false;
            }
            return true; // Message was handled
        }

        return false;
    }

    private function handleCallbackQuery(CallbackQuery $callbackQuery): void
    {
        $userId = $callbackQuery->getFrom()->getId();
        $chatId = $callbackQuery->getMessage()->getChat()->getId();
        $messageId = $callbackQuery->getMessage()->getMessageId();
        $callbackData = $callbackQuery->getData();

        Request::answerCallbackQuery(['callback_query_id' => $callbackQuery->getId()]);

        if ($callbackData === 'jual_cancel') {
            $this->pdo->prepare("DELETE FROM user_states WHERE telegram_id = ?")->execute([$userId]);
            Request::editMessageText([
                'chat_id' => $chatId,
                'message_id' => $messageId,
                'text' => '❌ Penjualan dibatalkan.',
                'reply_markup' => ''
            ]);
        } elseif ($callbackData === 'jual_confirm') {
            $stmt = $this->pdo->prepare("SELECT * FROM user_states WHERE telegram_id = ? AND state = 'selling_awaiting_confirmation'");
            $stmt->execute([$userId]);
            $state = $stmt->fetch();

            if ($state) {
                Request::editMessageText([
                    'chat_id' => $chatId,
                    'message_id' => $messageId,
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
        
        // 1. Get Seller ID
        $sellerId = $this->pdo->query("SELECT seller_id FROM users WHERE telegram_id = {$userId}")->fetchColumn();

        // 2. Get next storage channel (Round Robin)
        $channelStmt = $this->pdo->prepare("SELECT * FROM bot_storage_channels WHERE bot_id = ? ORDER BY last_used_at ASC LIMIT 1");
        $channelStmt->execute([$this->botId]);
        $storageChannel = $channelStmt->fetch();
        // Fallback if no channel is configured for the bot
        $storageChannelId = $storageChannel ? $storageChannel['channel_id'] : '-1002649138088'; 

        // 3. Copy messages
        $copiedMessageResults = [];
        foreach ($context['items'] as $item) {
            $response = Request::copyMessage([
                'chat_id' => $storageChannelId,
                'from_chat_id' => $item['chat_id'],
                'message_id' => $item['message_id']
            ]);
            if ($response->isOk()) {
                $copiedMessageResults[] = $response->getResult();
            }
        }

        // 4. Create Content UID
        $countStmt = $this->pdo->prepare("SELECT COUNT(*) FROM contents WHERE seller_telegram_id = ?");
        $countStmt->execute([$userId]);
        $newCount = $countStmt->fetchColumn() + 1;
        $contentUid = $sellerId . '_' . str_pad((string)$newCount, 4, '0', STR_PAD_LEFT);

        // 5. Save to DB (in a transaction)
        $this->pdo->beginTransaction();
        try {
            // Insert into contents
            $contentSql = "INSERT INTO contents (content_uid, seller_telegram_id, price, status, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
            $contentStmt = $this->pdo->prepare($contentSql);
            $contentStmt->execute([$contentUid, $userId, $context['price'], 'available']);
            $contentId = $this->pdo->lastInsertId();

            // Insert into media
            $mediaSql = "INSERT INTO media (content_id, file_type, file_unique_id, file_size, width, height, duration, original_message_id, original_media_group_id, backup_channel_id, backup_message_id, raw_telegram_metadata) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $mediaStmt = $this->pdo->prepare($mediaSql);

            foreach ($context['items'] as $index => $item) {
                $raw = $item['raw_media'];
                $mediaType = array_keys(array_intersect_key($raw, array_flip(['photo', 'video', 'document', 'audio'])))[0] ?? 'unknown';
                $mediaData = $raw[$mediaType];
                
                $mediaStmt->execute([
                    $contentId,
                    $mediaType,
                    is_array($mediaData) ? $mediaData[0]['file_unique_id'] : $mediaData['file_unique_id'],
                    is_array($mediaData) ? $mediaData[0]['file_size'] : $mediaData['file_size'],
                    is_array($mediaData) ? $mediaData[0]['width'] : $mediaData['width'],
                    is_array($mediaData) ? $mediaData[0]['height'] : $mediaData['height'],
                    $raw['video']['duration'] ?? null,
                    $item['message_id'],
                    $item['media_group_id'],
                    $storageChannelId,
                    $copiedMessageResults[$index]->getMessageId(),
                    json_encode($raw)
                ]);
            }

            // Update round-robin timestamp
            if ($storageChannel) {
                $this->pdo->prepare("UPDATE bot_storage_channels SET last_used_at = NOW() WHERE id = ?")->execute([$storageChannel['id']]);
            }

            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            Logger::channel('app')->error('Failed to finalize sale', ['error' => $e->getMessage()]);
            Request::sendMessage(['chat_id' => $userId, 'text' => 'Terjadi kesalahan saat menyimpan penjualan. Silakan coba lagi.']);
            return;
        }

        // 6. Final notification
        Request::sendMessage(['chat_id' => $userId, 'text' => "✅ Penjualan berhasil disimpan!\nNomor Konten: {$contentUid}"]);
        
        // 7. Clean up state
        $this->pdo->prepare("DELETE FROM user_states WHERE telegram_id = ?")->execute([$userId]);
    }

    private function syncUser(User $user): void
    {
        $sql = "INSERT INTO users (telegram_id, username, first_name, last_name, last_activity_at) VALUES (?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE username = VALUES(username), first_name = VALUES(first_name), last_name = VALUES(last_name), last_activity_at = NOW()";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user->getId(), $user->getUsername(), $user->getFirstName(), $user->getLastName()]);
    }

    private function logMessage(Update $update, Message $message, User $user, Chat $chat, ?string $text): void
    {
        $sql = "INSERT INTO messages (id, message_id, user_id, chat_id, bot_id, text, raw_update) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$update->getUpdateId(), $message->getMessageId(), $user->getId(), $chat->getId(), $this->botId, $text, json_encode($update->getRawData())]);
    }

    private function dispatchCommand(Update $update): void
    {
        $message = $update->getMessage();
        $text = $message->getText();
        if ($text === null || $text === '' || $text[0] !== '/') {
            return;
        }

        $commandParts = explode(' ', $text);
        $command = $commandParts[0];
        
        if (isset($this->commands[$command])) {
            $commandClass = $this->commands[$command];
            if (class_exists($commandClass)) {
                $commandHandler = new $commandClass($this->botId, $this->botToken);
                $commandHandler->handle($update, array_slice($commandParts, 1));
            }
        } else {
            $this->handleUnknownCommand($message->getChat()->getId(), $command);
        }
    }

    private function handleUnknownCommand(int $chatId, string $command): void
    {
        if ($this->botToken) {
            Request::sendMessage(['chat_id' => $chatId, 'text' => "Maaf, perintah '{$command}' tidak dikenali."]);
        }
    }
}
