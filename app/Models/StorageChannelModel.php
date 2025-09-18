<?php

namespace TokoBot\Models;

use PDO;
use TokoBot\Exceptions\DatabaseException;

class StorageChannelModel extends BaseModel
{
    protected string $table = 'bot_storage_channels';

    /**
     * Get all storage channels sorted by bot_id and id.
     *
     * @return array
     */
    public static function getAllSorted(): array
    {
        try {
            $pdo = self::getDb();
            $stmt = $pdo->query("SELECT id, bot_id, channel_id, last_used_at FROM bot_storage_channels ORDER BY bot_id ASC, id ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new DatabaseException("Error getting all sorted storage channels: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Find the least recently used storage channel for a bot.
     *
     * @param integer $botId
     * @return array|false
     */
    public static function findAvailableForBot(int $botId): array|false
    {
        try {
            $pdo = self::getDb();
            $stmt = $pdo->prepare("SELECT * FROM bot_storage_channels WHERE bot_id = ? ORDER BY last_used_at ASC LIMIT 1");
            $stmt->execute([$botId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new DatabaseException("Error finding available storage channel: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Update the last used timestamp for a channel.
     *
     * @param integer $channelRecordId
     * @return boolean
     */
    public static function updateLastUsed(int $channelRecordId): bool
    {
        try {
            $pdo = self::getDb();
            $stmt = $pdo->prepare("UPDATE bot_storage_channels SET last_used_at = NOW() WHERE id = ?");
            return $stmt->execute([$channelRecordId]);
        } catch (\PDOException $e) {
            throw new DatabaseException("Error updating last used timestamp for storage channel: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }
}
