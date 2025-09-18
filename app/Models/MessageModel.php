<?php

namespace TokoBot\Models;

use PDO;

class MessageModel extends BaseModel
{
    protected string $table = 'messages';

    /**
     * Get command usage statistics.
     *
     * @param int $limit
     * @return array
     */
    public static function getCommandUsageStats(int $limit = 10): array
    {
        try {
            $pdo = self::getDb();
            $stmt = $pdo->query("\n                SELECT text, count(*) as command_count \n                FROM messages \n                WHERE text LIKE '/%' \n                GROUP BY text \n                ORDER BY command_count DESC \n                LIMIT {$limit}\n            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \TokoBot\Exceptions\DatabaseException("Error getting command usage stats: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Log a message to the database.
     *
     * @param array $data
     * @return boolean
     */
    public static function logMessage(array $data): bool
    {
        try {
            $pdo = self::getDb();
            $sql = "INSERT INTO messages (id, message_id, user_id, chat_id, bot_id, text, raw_update) 
                    VALUES (?, ?, ?, ?, ?, ?, ?) 
                    ON DUPLICATE KEY UPDATE 
                        message_id = VALUES(message_id), 
                        user_id = VALUES(user_id), 
                        chat_id = VALUES(chat_id), 
                        bot_id = VALUES(bot_id), 
                        text = VALUES(text), 
                        raw_update = VALUES(raw_update)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $data['id'],
                $data['message_id'],
                $data['user_id'],
                $data['chat_id'],
                $data['bot_id'],
                $data['text'],
                $data['raw_update']
            ]);
        } catch (\PDOException $e) {
            throw new \TokoBot\Exceptions\DatabaseException("Error logging message: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }
}
