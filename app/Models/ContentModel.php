<?php

namespace TokoBot\Models;

use PDO;
use TokoBot\Exceptions\DatabaseException;

class ContentModel extends BaseModel
{
    protected string $table = 'contents';

    /**
     * Create a new content entry and return its ID.
     *
     * @param string $contentUid
     * @param integer $sellerTelegramId
     * @param float $price
     * @param string $status
     * @return integer
     */
    public static function createContent(string $contentUid, int $sellerTelegramId, float $price, string $status = 'available'): int
    {
        try {
            $pdo = self::getDb();
            $sql = "INSERT INTO contents (content_uid, seller_telegram_id, price, status, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
            $pdo->prepare($sql)->execute([$contentUid, $sellerTelegramId, $price, $status]);
            return (int)$pdo->lastInsertId();
        } catch (\PDOException $e) {
            throw new DatabaseException("Error creating content: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Count contents by a specific seller.
     *
     * @param integer $sellerTelegramId
     * @return integer
     */
    public static function countBySeller(int $sellerTelegramId): int
    {
        try {
            $pdo = self::getDb();
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM contents WHERE seller_telegram_id = ?");
            $stmt->execute([$sellerTelegramId]);
            return (int)$stmt->fetchColumn();
        } catch (\PDOException $e) {
            throw new DatabaseException("Error counting content by seller: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }
}
