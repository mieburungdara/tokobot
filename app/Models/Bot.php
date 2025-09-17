<?php

namespace TokoBot\Models;

use PDO;

class Bot extends BaseModel
{
    protected string \$table = 'tbots';

    /**
     * Find a bot token by its ID.
     *
     * @param int \$botId
     * @return string|null
     */
    public static function findTokenById(int \$botId): ?string
    {
        try {
            \$pdo = \TokoBot\Helpers\Database::getInstance();
            \$stmt = \$pdo->prepare("SELECT token FROM tbots WHERE id = ?");
            \$stmt->execute([\$botId]);
            \$result = \$stmt->fetch(PDO::FETCH_COLUMN);
            return \$result ?: null;
        } catch (\Exception \$e) {
            \TokoBot\Helpers\Logger::channel('database')->error('Failed to find bot token', ['bot_id' => \$botId, 'error' => \$e->getMessage()]);
            return null;
        }
    }
}
