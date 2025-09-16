<?php

namespace TokoBot\BotHandlers;

use TokoBot\Helpers\Database;
use TelegramBot\Telegram;
use TelegramBot\Request;
use TokoBot\Helpers\Logger;

class GenericBotHandler
{
    protected int $botId;

    public function __construct(array $botConfig)
    {
        $this->botId = $botConfig['id'];
    }

    public function handle()
    {
        try {
            $pdo = Database::getInstance();
            $update = Telegram::getUpdate();

            $message = $update ? $update->getMessage() : null;
            if (!$message) {
                return; // Not a message update, ignore
            }
            $user = $message->getFrom();
            $chat = $message->getChat();
            $text = $message->getText();

            // 1. Sinkronisasi Pengguna
            $sql = "INSERT INTO users (telegram_id, username, first_name, last_name) VALUES (?, ?, ?, ?) "
                 . "ON DUPLICATE KEY UPDATE username = VALUES(username), "
                 . "first_name = VALUES(first_name), last_name = VALUES(last_name)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $user->getId(),
                $user->getUsername(),
                $user->getFirstName(),
                $user->getLastName()
            ]);
            Logger::channel('app')->info("User {$user->getId()} ({$user->getUsername()}) synced.");

            // 2. Log Pesan
            $sql = "INSERT INTO messages (id, message_id, user_id, chat_id, bot_id, text, raw_update) "
                 . "VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
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
        $pdo = Database::getInstance();

        // Buat token acak yang aman
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);

        // Atur kedaluwarsa 5 menit dari sekarang
        $expires = new \DateTime();
        $expires->add(new \DateInterval('PT5M'));
        $expiresAt = $expires->format('Y-m-d H:i:s');

        // Simpan hash dari token dan waktu kedaluwarsa ke database
        $sql = "UPDATE users SET login_token = ?, token_expires_at = ? WHERE telegram_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tokenHash, $expiresAt, $userId]);

        // Ambil token bot dari file config untuk mengirim balasan
        $botsFile = CONFIG_PATH . '/tbots.php';
        $botTokens = file_exists($botsFile) ? require $botsFile : [];
        $botToken = $botTokens[$this->botId] ?? null;

        if ($botToken) {
            // Buat URL login
            $loginUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'your-domain.com') . '/login/' . $token;
            $text = "Ini adalah link login sekali pakai Anda. Link akan kedaluwarsa dalam 5 menit:\n\n" . $loginUrl;

            new Telegram($botToken);
            Request::sendMessage([
                'chat_id' => $userId,
                'text' => $text
            ]);
        }
    }
}
