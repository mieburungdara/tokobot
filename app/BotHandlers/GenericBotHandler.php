<?php

namespace TokoBot\BotHandlers;

use TokoBot\BotHandlers\Commands\LoginCommand;
use TokoBot\BotHandlers\Commands\StartCommand;
use TokoBot\Helpers\Database;
use TelegramBot\Telegram;
use TelegramBot\Request;
use TokoBot\Helpers\Logger;
use TelegramBot\Entities\Update;
use TelegramBot\Entities\User;
use TelegramBot\Types\Message;
use TelegramBot\Types\Chat;

class GenericBotHandler
{
    protected int $botId;
    protected ?string $botToken;
    protected ?\PDO $pdo;

    /**
     * @var array<string, string> Command mapping
     */
    protected array $commands = [
        '/login' => LoginCommand::class,
        '/start' => StartCommand::class,
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

            $message = $update ? $update->getMessage() : null;
            if (!$message) {
                return; // Not a message update, ignore
            }
            $user = $message->getFrom();
            $chat = $message->getChat();
            $text = $message->getText();

            // 1. Sync User
            $this->syncUser($user);

            // 2. Log Message
            $this->logMessage($update, $message, $user, $chat, $text);

            // 3. Handle Command
            $this->dispatchCommand($update);

        } catch (\Exception $e) {
            Logger::channel('app')->error("Bot Handler Error for Bot ID {$this->botId}", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function syncUser(User $user): void
    {
        $sql = "INSERT INTO users (telegram_id, username, first_name, last_name, last_activity_at) "
             . "VALUES (?, ?, ?, ?, NOW()) "
             . "ON DUPLICATE KEY UPDATE username = VALUES(username), "
             . "first_name = VALUES(first_name), last_name = VALUES(last_name), "
             . "last_activity_at = NOW()";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $user->getId(),
            $user->getUsername(),
            $user->getFirstName(),
            $user->getLastName(),
        ]);
        Logger::channel('app')->info("User {$user->getId()} ({$user->getUsername()}) synced.");

        $sqlBotUser = "INSERT INTO bot_user (bot_id, user_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE last_accessed_at = NOW()";
        $stmtBotUser = $this->pdo->prepare($sqlBotUser);
        $stmtBotUser->execute([$this->botId, $user->getId()]);
    }

    private function logMessage(Update $update, Message $message, User $user, Chat $chat, ?string $text): void
    {
        $sql = "INSERT INTO messages (id, message_id, user_id, chat_id, bot_id, text, raw_update) "
             . "VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $update->getUpdateId(),
            $message->getMessageId(),
            $user->getId(),
            $chat->getId(),
            $this->botId,
            $text,
            json_encode($update->getRawData())
        ]);
    }

    private function dispatchCommand(Update $update): void
    {
        $message = $update->getMessage();
        $text = $message->getText();
        if ($text === null || $text === '' || $text[0] !== '/') {
            return; // Ignore non-command messages
        }

        $commandParts = explode(' ', $text);
        $command = $commandParts[0];
        $args = array_slice($commandParts, 1);

        if (isset($this->commands[$command])) {
            $commandClass = $this->commands[$command];
            if (class_exists($commandClass)) {
                $commandHandler = new $commandClass($this->botId, $this->botToken);
                $commandHandler->handle($update, $args);
                Logger::channel('app')->info("Dispatched command '{$command}' to {$commandClass}");
            } else {
                Logger::channel('app')->warning("Command class {$commandClass} not found for command '{$command}'");
            }
        } else {
            $this->handleUnknownCommand($message->getChat()->getId(), $command);
        }
    }

    private function handleUnknownCommand(int $chatId, string $command): void
    {
        Logger::channel('app')->info("Unknown command '{$command}' received.");
        if ($this->botToken) {
            new Telegram($this->botToken);
            Request::sendMessage([
                'chat_id' => $chatId,
                'text' => "Maaf, perintah '{$command}' tidak dikenali.",
            ]);
        }
    }
}