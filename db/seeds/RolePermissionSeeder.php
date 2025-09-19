<?php

use Phinx\Seed\AbstractSeed;

class RolePermissionSeeder extends AbstractSeed
{
    public function run(): void
    {
        // --- CONFIGURATION ---
        $rolesConfig = require(dirname(__DIR__, 2) . '/config/roles.php');

        // --- TRUNCATE TABLES ---
        $this->execute('SET FOREIGN_KEY_CHECKS=0');
        $this->table('role_permissions')->truncate();
        $this->table('permissions')->truncate();
        $this->execute('SET FOREIGN_KEY_CHECKS=1');

        // --- SEED PERMISSIONS ---
        $permissions = [];
        foreach ($rolesConfig as $roleData) {
            foreach ($roleData['permissions'] as $permissionName) {
                $permissions[$permissionName] = ['name' => $permissionName];
            }
        }
        $this->table('permissions')->insert(array_values($permissions))->save();

        // --- SEED ROLE-PERMISSION LINKS ---

        // 1. Get all roles and permissions from DB with their IDs
        $rolesInDb = $this->fetchAll('SELECT id, name FROM roles');
        $permissionsInDb = $this->fetchAll('SELECT id, name FROM permissions');

        // 2. Create a map for easy lookup
        $roleMap = array_column($rolesInDb, 'id', 'name');
        $permissionMap = array_column($permissionsInDb, 'id', 'name');

        // 3. Prepare data for pivot table
        $rolePermissionsData = [];
        foreach ($rolesConfig as $roleName => $roleData) {
            if (isset($roleMap[$roleName])) {
                $roleId = $roleMap[$roleName];
                foreach ($roleData['permissions'] as $permissionName) {
                    if (isset($permissionMap[$permissionName])) {
                        $permissionId = $permissionMap[$permissionName];
                        $rolePermissionsData[] = [
                            'role_id' => $roleId,
                            'permission_id' => $permissionId,
                        ];
                    }
                }
            }
        }

        // 4. Insert into pivot table
        if (!empty($rolePermissionsData)) {
            $this->table('role_permissions')->insert($rolePermissionsData)->save();
        }
    }
}