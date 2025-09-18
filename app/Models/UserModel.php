<?php

namespace TokoBot\Models;

use PDO;
use TokoBot\Exceptions\DatabaseException;

class UserModel extends BaseModel
{
    protected string $table = 'users';

    /**
     * Find a user by their Telegram ID.
     *
     * @param int $telegramId
     * @return array|false
     */
    public static function findByTelegramId(int $telegramId): array|false
    {
        try {
            $pdo = self::getDb();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE telegram_id = ?");
            $stmt->execute([$telegramId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new DatabaseException("Error finding user by Telegram ID: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Get all users with their assigned role names.
     *
     * @return array
     */
    public static function getAllWithRoles(): array
    {
        try {
            $pdo = self::getDb();
            $stmt = $pdo->query("
                SELECT u.telegram_id, u.username, u.first_name, r.name as role_name 
                FROM users u 
                LEFT JOIN roles r ON u.role_id = r.id 
                ORDER BY u.first_name ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new DatabaseException("Error getting all users with roles: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Get recently active users.
     *
     * @param int $limit
     * @return array
     */
    public static function getRecentlyActiveUsers(int $limit = 10): array
    {
        try {
            $pdo = self::getDb();
            $stmt = $pdo->query("
                SELECT username, first_name, last_activity_at 
                FROM users 
                WHERE last_activity_at IS NOT NULL 
                ORDER BY last_activity_at DESC 
                LIMIT {$limit}
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new DatabaseException("Error getting recently active users: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Update a user\'s role by their Telegram ID.
     *
     * @param int $telegramId
     * @param int $roleId
     * @return bool
     */
    public static function updateRoleByTelegramId(int $telegramId, int $roleId): bool
    {
        try {
            $pdo = self::getDb();
            $stmt = $pdo->prepare("UPDATE users SET role_id = ? WHERE telegram_id = ?");
            return $stmt->execute([$roleId, $telegramId]);
        } catch (\PDOException $e) {
            throw new DatabaseException("Error updating user role: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Find a seller ID by Telegram ID.
     *
     * @param int $telegramId
     * @return string|null
     */
    public static function findSellerIdByTelegramId(int $telegramId): ?string
    {
        try {
            $pdo = self::getDb();
            $stmt = $pdo->prepare("SELECT seller_id FROM users WHERE telegram_id = ?");
            $stmt->execute([$telegramId]);
            $result = $stmt->fetchColumn();
            return $result ?: null;
        } catch (\PDOException $e) {
            throw new DatabaseException("Error finding seller ID: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Sync user data from Telegram Mini App.
     *
     * @param array $userData
     * @return bool
     */
    public static function syncFromTelegram(array $userData): bool
    {
        try {
            $pdo = self::getDb();
            $sql = "INSERT INTO users (telegram_id, username, first_name, last_name, photo_url, language_code, last_activity_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW()) 
                    ON DUPLICATE KEY UPDATE 
                        username = VALUES(username), 
                        first_name = VALUES(first_name), 
                        last_name = VALUES(last_name), 
                        photo_url = VALUES(photo_url), 
                        language_code = VALUES(language_code), 
                        last_activity_at = NOW()";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $userData['id'],
                $userData['username'] ?? null,
                $userData['first_name'],
                $userData['last_name'] ?? null,
                $userData['photo_url'] ?? null,
                $userData['language_code'] ?? 'en'
            ]);
        } catch (\PDOException $e) {
            throw new DatabaseException("Error syncing user from Telegram: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Sync user data from a Telegram User object.
     *
     * @param \Longman\TelegramBot\Entities\User $user
     * @return boolean
     */
    public static function syncUser(\Longman\TelegramBot\Entities\User $user): bool
    {
        try {
            $pdo = self::getDb();
            $sql = "INSERT INTO users (telegram_id, username, first_name, last_name, last_activity_at) 
                    VALUES (?, ?, ?, ?, NOW()) 
                    ON DUPLICATE KEY UPDATE 
                        username = VALUES(username), 
                        first_name = VALUES(first_name), 
                        last_name = VALUES(last_name), 
                        last_activity_at = NOW()";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $user->getId(), 
                $user->getUsername(), 
                $user->getFirstName(), 
                $user->getLastName()
            ]);
        } catch (\PDOException $e) {
            throw new DatabaseException("Error syncing user: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }
}
