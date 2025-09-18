<?php

namespace TokoBot\Models;

use PDO;
use TokoBot\Exceptions\DatabaseException;

class UserStateModel extends BaseModel
{
    protected string $table = 'user_states';

    /**
     * Find the current state for a user.
     *
     * @param int $telegramId
     * @return array|false
     */
    public static function findByTelegramId(int $telegramId): array|false
    {
        try {
            $pdo = self::getDb();
            $stmt = $pdo->prepare("SELECT * FROM user_states WHERE telegram_id = ?");
            $stmt->execute([$telegramId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOExceptiabase $e) {
            throw new DatabaseException("Error finding user state: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Create or update a user\'s state.
     *
     * @param int $telegramId
     * @param string $state
     * @param array $context
     * @return boolean
     */
    public static function updateState(int $telegramId, string $state, array $context = []): bool
    {
        try {
            $pdo = self::getDb();
            $sql = "INSERT INTO user_states (telegram_id, state, context) VALUES (?, ?, ?) 
                    ON DUPLICATE KEY UPDATE state = VALUES(state), context = VALUES(context)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$telegramId, $state, json_encode($context)]);
        } catch (PDOExceptiabase $e) {
            throw new DatabaseException("Error updating user state: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Clear a user\'s state.
     *
     * @param int $telegramId
     * @return boolean
     */
    public static function clearState(int $telegramId): bool
    {
        try {
            $pdo = self::getDb();
            $stmt = $pdo->prepare("DELETE FROM user_states WHERE telegram_id = ?");
            return $stmt->execute([$telegramId]);
        } catch (PDOExceptiabase $e) {
            throw new DatabaseException("Error clearing user state: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }
}
