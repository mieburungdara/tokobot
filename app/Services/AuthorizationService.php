<?php

namespace TokoBot\Services;

use TokoBot\Helpers\Database;
use TokoBot\Helpers\Session;

class AuthorizationService
{
    /** @var array<string, array<string, mixed>> */
    private array $roles = [];

    /** @var array<string, array<string>> */
    private array $permissionsByRole = [];

    public function __construct()
    {
        $this->loadRolesAndPermissionsFromDb();
    }

    private function loadRolesAndPermissionsFromDb(): void
    {
        try {
            $pdo = Database::getInstance();
            $rolesData = $pdo->query("SELECT id, name FROM roles")->fetchAll(\PDO::FETCH_ASSOC);
            $permissionsData = $pdo->query("SELECT id, name FROM permissions")->fetchAll(\PDO::FETCH_ASSOC);
            $rolePermissionsData = $pdo->query("SELECT r.name as role_name, p.name as permission_name FROM role_permissions rp JOIN roles r ON rp.role_id = r.id JOIN permissions p ON rp.permission_id = p.id")->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($rolesData as $role) {
                $this->roles[$role['name']] = $role;
                $this->permissionsByRole[$role['name']] = [];
            }

            foreach ($rolePermissionsData as $rp) {
                $this->permissionsByRole[$rp['role_name']][] = $rp['permission_name'];
            }

            // Handle inheritance - this is a simple, single-level inheritance
            $rolesConfig = require CONFIG_PATH . '/roles.php';
            foreach ($rolesConfig as $roleName => $config) {
                if (!empty($config['inherits'])) {
                    foreach ($config['inherits'] as $parentRole) {
                        if (isset($this->permissionsByRole[$parentRole])) {
                            $this->permissionsByRole[$roleName] = array_merge(
                                $this->permissionsByRole[$roleName],
                                $this->permissionsByRole[$parentRole]
                            );
                        }
                    }
                }
                $this->permissionsByRole[$roleName] = array_unique($this->permissionsByRole[$roleName]);
            }

        } catch (\Exception $e) {
            // If DB fails, service will have no permissions. Potentially log this.
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
