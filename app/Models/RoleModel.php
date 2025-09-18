<?php

namespace TokoBot\Models;

use TokoBot\Exceptions\DatabaseException;

class RoleModel extends BaseModel
{
    protected string $table = 'roles';

    /**
     * Get all roles sorted by name.
     *
     * @return array
     */
    public static function getAllSortedByName(): array
    {
        try {
            $pdo = self::getDb();
            $stmt = $pdo->query("SELECT id, name FROM roles ORDER BY name ASC");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new DatabaseException("Error getting all roles: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }
}
