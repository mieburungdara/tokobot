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
            $pdo = self::getDb();
            $stmt = $pdo->prepare("SELECT token FROM tbots WHERE id = ?");
            $stmt->execute([$botId]);
            $result = $stmt->fetch(PDO::FETCH_COLUMN);
            return $result ?: null;
        } catch (\PDOException $e) {
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
            $pdo = self::getDb();
            $stmt = $pdo->prepare("
                SELECT b.id, b.username, b.first_name, b.token IS NOT NULL as has_token
                FROM tbots b
                INNER JOIN bot_user bu ON b.id = bu.bot_id
                WHERE bu.user_id = ?
                ORDER BY b.first_name ASC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            \TokoBot\Helpers\Logger::channel('database')->error('Failed to find bots by user ID', ['user_id' => $userId, 'error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get total count of bots.
     *
     * @return integer
     */
    public static function countAll(): int
    {
        try {
            $pdo = self::getDb();
            return (int) $pdo->query("SELECT count(*) FROM tbots")->fetchColumn();
        } catch (\PDOException $e) {
            throw new \TokoBot\Exceptions\DatabaseException("Error counting bots: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Get all bots with token status for management.
     *
     * @return array
     */
    public static function findAllWithTokenStatus(): array
    {
        try {
            $pdo = self::getDb();
            $stmt = $pdo->query("SELECT id, username, first_name, token IS NOT NULL as has_token FROM tbots ORDER BY first_name ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \TokoBot\Exceptions\DatabaseException("Error finding all bots with token status: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Get all bots for a selection dropdown.
     *
     * @return array
     */
    public static function findAllForSelection(): array
    {
        try {
            $pdo = self::getDb();
            $stmt = $pdo->query("SELECT id, username FROM tbots ORDER BY username ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \TokoBot\Exceptions\DatabaseException("Error finding all bots for selection: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }
}
