<?php

namespace TokoBot\Services;

use TokoBot\Helpers\Database;
use TokoBot\Helpers\Session;
use Psr\SimpleCache\CacheInterface;

class AuthorizationService
{
    /** @var array<string, array<string, mixed>> */
    private array $roles = [];

    /** @var array<string, array<string>> */
    private array $permissionsByRole = [];

    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
        $this->loadRolesAndPermissions();
    }

    private function loadRolesAndPermissions(): void
    {
        $cacheKey = 'auth_data';
        $cachedData = $this->cache->get($cacheKey);

        if ($cachedData !== null) {
            $this->roles = $cachedData['roles'];
            $this->permissionsByRole = $cachedData['permissionsByRole'];
            return;
        }

        try {
            $pdo = Database::getInstance();
            $query = "
                SELECT
                    r.id, r.name,
                    GROUP_CONCAT(p.name SEPARATOR ',') as permissions
                FROM
                    roles r
                LEFT JOIN
                    role_permissions rp ON r.id = rp.role_id
                LEFT JOIN
                    permissions p ON rp.permission_id = p.id
                GROUP BY
                    r.id, r.name
            ";
            $rolesData = $pdo->query($query)->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($rolesData as $role) {
                $this->roles[$role['name']] = ['id' => $role['id'], 'name' => $role['name']];
                $this->permissionsByRole[$role['name']] = $role['permissions'] ? explode(',', $role['permissions']) : [];
            }

            // Handle inheritance
            $rolesConfig = require CONFIG_PATH . '/roles.php';
            foreach ($rolesConfig as $roleName => $config) {
                if (isset($this->permissionsByRole[$roleName]) && !empty($config['inherits'])) {
                    foreach ($config['inherits'] as $parentRole) {
                        if (isset($this->permissionsByRole[$parentRole])) {
                            $this->permissionsByRole[$roleName] = array_merge(
                                $this->permissionsByRole[$roleName],
                                $this->permissionsByRole[$parentRole]
                            );
                        }
                    }
                }
                if (isset($this->permissionsByRole[$roleName])) {
                    $this->permissionsByRole[$roleName] = array_unique($this->permissionsByRole[$roleName]);
                }
            }

            // Store the processed data in cache
            $this->cache->set($cacheKey, [
                'roles' => $this->roles,
                'permissionsByRole' => $this->permissionsByRole
            ]);

        } catch (\Exception $e) {
            $this->roles = [];
            $this->permissionsByRole = [];
        }
    }

    /**
     * Check if the current user has a specific permission.
     *
     * @param string $permissionName
     * @return bool
     */
    public function can(string $permissionName): bool
    {
        $userRole = Session::get('user_role', 'guest');
        return in_array($permissionName, $this->permissionsByRole[$userRole] ?? []);
    }

    /**
     * Check if the current user has ANY of the given roles.
     *
     * @param array $roles
     * @return bool
     */
    public function any(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->check($role)) {
                return true;
            }
        }
        return false;
    }
}
