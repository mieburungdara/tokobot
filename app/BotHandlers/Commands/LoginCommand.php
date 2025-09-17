<?php

namespace TokoBot\BotHandlers\Commands;

use TokoBot\Helpers\Logger;
use TokoBot\Helpers\Database;
use TelegramBot\Types\Update;
use TelegramBot\Telegram;
use TelegramBot\Request;

class LoginCommand implements CommandInterface
{
    protected ?\PDO $pdo;
    protected int $botId;
    protected ?string $botToken;

    public function __construct(int $botId, ?string $botToken)
    {
        $this->pdo = Database::getInstance();
        $this->botId = $botId;
        $this->botToken = $botToken;
    }

    public function handle(Update $update, array $args = []): void
    {
        $user = $update->getMessage()->getFrom();
        $userId = $user->getId();

        Logger::channel('app')->info("Initiating login token generation for user: {$userId}");

        // Buat token acak yang aman
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        Logger::channel('app')->debug("Generated token hash for user: {$userId}");

        // Store the token hash in the database. Token will not expire.
        $sql = "UPDATE users SET login_token = ? WHERE telegram_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$tokenHash, $userId]);
        Logger::channel('app')->info("Stored login token in DB for user: {$userId}. Token will not expire.");

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
