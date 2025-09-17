<?php

namespace TokoBot\Models;

use PDO;

class Bot extends BaseModel
{
    protected string $table = 'tbots';

    /**
     * Find a bot token by its ID.
     *
     * @param int $botId
     * @return string|null
     */
    public static function findTokenById(int $botId): ?string
    {
        try {
            $pdo = \TokoBot\Helpers\Database::getInstance();
            $stmt = $pdo->prepare("SELECT token FROM tbots WHERE id = ?");
            $stmt->execute([$botId]);
            $result = $stmt->fetch(PDO::FETCH_COLUMN);
            return $result ?: null;
        } catch (\Exception $e) {
            \TokoBot\Helpers\Logger::channel('database')->error('Failed to find bot token', ['bot_id' => $botId, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Find all bots associated with a user.
     *
     * @param int $userId
     * @return array
     */
    public static function findByUserId(int $userId): array
    {
        try {
            $pdo = \TokoBot\Helpers\Database::getInstance();
            $stmt = $pdo->prepare("
                SELECT b.id, b.username, b.first_name, b.token IS NOT NULL as has_token
                FROM tbots b
                INNER JOIN bot_user bu ON b.id = bu.bot_id
                WHERE bu.user_id = ?
                ORDER BY b.first_name ASC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            \TokoBot\Helpers\Logger::channel('database')->error('Failed to find bots by user ID', ['user_id' => $userId, 'error' => $e->getMessage()]);
            return [];
        }
    }
}
