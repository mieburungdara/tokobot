<?php

namespace TokoBot\Models;

class PermissionModel extends BaseModel
{
    protected string $table = 'permissions';

    /**
     * Get all permissions, sorted by name.
     *
     * @return array
     */
    public static function getAllSortedByName(): array
    {
        $stmt = self::getDb()->query("SELECT * FROM permissions ORDER BY name ASC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
