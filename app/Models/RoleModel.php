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

    /**
     * Get all roles and their assigned permissions.
     *
     * @return array
     */
    public static function getAllWithPermissions(): array
    {
        $roles = self::getAllSortedByName();
        $stmt = self::getDb()->query("SELECT role_id, permission_id FROM role_permissions");
        $rolePermissions = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $permissionsByRole = [];
        foreach ($rolePermissions as $rp) {
            $permissionsByRole[$rp['role_id']][] = $rp['permission_id'];
        }

        foreach ($roles as &$role) {
            $role['permissions'] = $permissionsByRole[$role['id']] ?? [];
        }

        return $roles;
    }

    /**
     * Sync permissions for a given role.
     *
     * @param int $roleId
     * @param array $permissionIds
     */
    public static function syncPermissions(int $roleId, array $permissionIds): void
    {
        $pdo = self::getDb();
        $pdo->beginTransaction();
        try {
            // Delete old permissions
            $deleteStmt = $pdo->prepare("DELETE FROM role_permissions WHERE role_id = :role_id");
            $deleteStmt->execute(['role_id' => $roleId]);

            // Insert new permissions
            if (!empty($permissionIds)) {
                $insertStmt = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)");
                foreach ($permissionIds as $permissionId) {
                    $insertStmt->execute(['role_id' => $roleId, 'permission_id' => $permissionId]);
                }
            }
            $pdo->commit();
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw new DatabaseException("Error syncing permissions: " . $e->getMessage());
        }
    }
}
