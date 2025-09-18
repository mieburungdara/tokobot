<?php

namespace TokoBot\Models;

use TokoBot\Exceptions\DatabaseException;

class MediaModel extends BaseModel
{
    protected string $table = 'media';

    /**
     * Create a new media entry.
     *
     * @param array $data
     * @return boolean
     */
    public static function createMedia(array $data): bool
    {
        try {
            $pdo = self::getDb();
            $sql = "INSERT INTO media (content_id, file_type, file_unique_id, file_size, width, height, duration, original_message_id, original_media_group_id, backup_channel_id, backup_message_id, raw_telegram_metadata) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $data['content_id'],
                $data['file_type'],
                $data['file_unique_id'],
                $data['file_size'] ?? null,
                $data['width'] ?? null,
                $data['height'] ?? null,
                $data['duration'] ?? null,
                $data['original_message_id'],
                $data['original_media_group_id'],
                $data['backup_channel_id'],
                $data['backup_message_id'],
                $data['raw_telegram_metadata']
            ]);
        } catch (\PDOException $e) {
            throw new DatabaseException("Error creating media: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }
}
