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
        $input = json_decode($this->telegram->getCustomInput(), true) ?: [];
        $update = new Update($input, $this->telegram->getBotUsername());

        //$update = new Update(json_decode($this->telegram->getCustomInput(), true), $this->telegram->getBotUsername());
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

        UserModel::syncUser($user);

        // If not handled statefully or as a callback query, proceed to command handling
        $this->telegram->addCommandsPath(ROOT_PATH . '/app/BotHandlers/Commands');
        
        // The handle() method in the library executes the command and returns a boolean.
        // We call it for its side-effect (running the command) and ignore the return value.
        $this->telegram->handle();

        // We must return a ServerResponse to satisfy the method's return type hint.
        // The actual HTTP response to Telegram has already been handled by the webhook template.
        return Request::emptyResponse();
    }


    private function handleStatefulMessage(Message $message): bool
    {
        $userId = $message->getFrom()->getId();
        $text = $message->getText();

        $state = UserStateModel::findByTelegramId($userId);

        if (!$state || $state['state'] !== \TokoBot\Helpers\BotState::SELLING_BATCHING_ITEMS) {
            return false;
        }

        $price = (float) str_replace(['Rp', '.', ','], ['', '', '.'], $text);

        if ($price > 0) {
            $context = json_decode($state['context'], true);
            $context['price'] = $price;

            UserStateModel::updateState($userId, \TokoBot\Helpers\BotState::SELLING_AWAITING_CONFIRMATION, $context);

            $itemCount = count($context['items']);
            $responseText = "Anda akan menjual paket berisi {$itemCount} item dengan harga Rp " . number_format($price, 0, ',', '.') . ". Lanjutkan?";
            Request::sendMessage([
                'chat_id' => $userId,
                'text' => $responseText,
                'reply_markup' => json_encode(['inline_keyboard' => [[['text' => '✅ Jual', 'callback_data' => 'jual_confirm'], ['text' => '❌ Batal', 'callback_data' => 'jual_cancel']]]])
            ]);
            return true; // Message was handled as price
        }

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
            UserStateModel::clearState($userId);
            Request::editMessageText([
                'chat_id' => $message->getChat()->getId(),
                'message_id' => $message->getMessageId(),
                'text' => '❌ Penjualan dibatalkan.',
                'reply_markup' => ''
            ]);
        } elseif ($callbackData === 'jual_confirm') {
            $state = UserStateModel::findByTelegramId($userId);

            if ($state && $state['state'] === \TokoBot\Helpers\BotState::SELLING_AWAITING_CONFIRMATION) {
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
            $sellerId = UserModel::findSellerIdByTelegramId($userId);

            $storageChannel = StorageChannelModel::findAvailableForBot($this->botId);
            $storageChannelId = $storageChannel ? (int)$storageChannel['channel_id'] : -1002649138088; // Fallback

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

            $newCount = ContentModel::countBySeller($userId) + 1;
            $contentUid = $sellerId . '_' . str_pad((string)$newCount, 4, '0', STR_PAD_LEFT);

            $contentId = ContentModel::createContent($contentUid, $userId, $context['price']);

            foreach ($context['items'] as $index => $item) {
                $raw = $item['raw_media'];
                $mediaType = array_keys(array_intersect_key($raw, array_flip(['photo', 'video', 'document', 'audio'])))[0] ?? 'unknown';
                $mediaData = $raw[$mediaType];
                $file = is_array($mediaData) ? end($mediaData) : $mediaData;

                MediaModel::createMedia([
                    'content_id' => $contentId,
                    'file_type' => $mediaType,
                    'file_unique_id' => $file['file_unique_id'],
                    'file_size' => $file['file_size'] ?? null,
                    'width' => $file['width'] ?? null,
                    'height' => $file['height'] ?? null,
                    'duration' => $file['duration'] ?? null,
                    'original_message_id' => $item['message_id'],
                    'original_media_group_id' => $item['media_group_id'],
                    'backup_channel_id' => $storageChannelId,
                    'backup_message_id' => $copiedMessageIds[$index],
                    'raw_telegram_metadata' => json_encode($raw)
                ]);
            }

            if ($storageChannel) {
                StorageChannelModel::updateLastUsed($storageChannel['id']);
            }

            $this->pdo->commit();

            Request::sendMessage(['chat_id' => $userId, 'text' => "✅ Penjualan berhasil disimpan!\nNomor Konten: {$contentUid}"]);
            UserStateModel::clearState($userId);

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            Logger::channel('app')->error('Failed to finalize sale', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            Request::sendMessage(['chat_id' => $userId, 'text' => 'Terjadi kesalahan fatal saat menyimpan penjualan. Silakan coba lagi.']);
        }
    }

    private function logMessage(Update $update): void
    {
        $message = $update->getMessage();
        MessageModel::logMessage([
            'id' => $update->getUpdateId(),
            'message_id' => $message->getMessageId(),
            'user_id' => $message->getFrom()->getId(),
            'chat_id' => $message->getChat()->getId(),
            'bot_id' => $this->botId,
            'text' => $message->getText(),
            'raw_update' => json_encode($update->getRawData())
        ]);
    }
}