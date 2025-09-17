<?php

namespace TokoBot\BotHandlers;

use TokoBot\Helpers\Database;
use TelegramBot\Telegram;
use TelegramBot\Request;
use TokoBot\Helpers\Logger;

class GenericBotHandler
{
    protected int $botId;
    protected ?string $botToken;
    protected ?\PDO $pdo;

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

            // 1. Sinkronisasi Pengguna
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

            // Tambahan: Catat interaksi di tabel relasi bot_user
            $sqlBotUser = "INSERT INTO bot_user (bot_id, user_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE last_accessed_at = NOW()";
            $stmtBotUser = $this->pdo->prepare($sqlBotUser);
            $stmtBotUser->execute([$this->botId, $user->getId()]);

            // 2. Log Pesan
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

            // 3. Tangani perintah /login
            if ($text === '/login') {
                $this->handleLoginCommand($user->getId());
            }
        } catch (\Exception $e) {
            // Log error menggunakan Monolog
            Logger::channel('app')->error("Bot Handler Error for Bot ID {$this->botId}", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function handleLoginCommand(int $userId)
    {
        Logger::channel('app')->info("Initiating login token generation for user: {$userId}");

        // Buat token acak yang aman
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        Logger::channel('app')->debug("Generated token hash for user: {$userId}");

        // Store the token hash in the database. Token will not expire.
        $sql = "UPDATE users SET login_token = ? WHERE telegram_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$tokenHash, $userId]);
        Logger::channel('app')->info("Stored login token in DB for user: {\$userId}. Token will not expire.");

        if ($this->botToken) {
            // Build the login URL. Use APP_URL from environment for consistency, with a fallback.
            // Ensure your .env file has an APP_URL variable (e.g., APP_URL=https://core.my.id).
            $appBaseUrl = rtrim($_ENV['APP_URL'] ?? 'https://' . ($_SERVER['HTTP_HOST'] ?? 'your-domain.com'), '/');
            $loginUrl = $appBaseUrl . '/login/' . $token . '?bot_id=' . $this->botId;
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'Login ke TokoBot', 'url' => $loginUrl]
                    ]
                ]
            ];

            $text = "Klik tombol di bawah untuk login ke TokoBot. Link ini berlaku selamanya.";

            new Telegram($this->botToken);
            Request::sendMessage([
                'chat_id' => $userId,
                'text' => $text,
                'reply_markup' => json_encode($keyboard)
            ]);
            Logger::channel('app')->info("Sent login link to user: {$userId}");
        } else {
            Logger::channel('app')->warning("Could not send login link to user {$userId}: Bot token is not configured.");
        }
    }
}
