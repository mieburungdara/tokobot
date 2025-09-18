<?php

namespace TokoBot\Models;

use PDO;
use TokoBot\Exceptions\DatabaseException;

class BotUserModel extends BaseModel
{
    protected string $table = 'bot_user';

    /**
     * Sync a user interaction with a bot.
     *
     * @param integer $botId
     * @param integer $userId
     * @param boolean $allowsWriteToPm
     * @return boolean
     */
    public static function syncInteraction(int $botId, int $userId, bool $allowsWriteToPm): bool
    {
        try {
            $pdo = self::getDb();
            $sql = "INSERT INTO bot_user (bot_id, user_id, allows_write_to_pm) VALUES (?, ?, ?) 
                    ON DUPLICATE KEY UPDATE last_accessed_at = NOW(), allows_write_to_pm = VALUES(allows_write_to_pm)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $botId, 
                $userId,
                $allowsWriteToPm ? 1 : 0
            ]);
        } catch (\PDOException $e) {
            throw new DatabaseException("Error syncing bot user interaction: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }
}
