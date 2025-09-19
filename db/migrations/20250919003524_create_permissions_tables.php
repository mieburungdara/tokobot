<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePermissionsTables extends AbstractMigration
{
    public function change(): void
    {
        // Create permissions table
        $permissions = $this->table('permissions');
        $permissions->addColumn('name', 'string', ['limit' => 100])
                    ->addColumn('description', 'string', ['null' => true])
                    ->addIndex(['name'], ['unique' => true])
                    ->create();

        // Create role_permissions pivot table
        $rolePermissions = $this->table('role_permissions', ['id' => false, 'primary_key' => ['role_id', 'permission_id']]);
        $rolePermissions->addColumn('role_id', 'integer')
                        ->addColumn('permission_id', 'integer')
                        ->addForeignKey('role_id', 'roles', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
                        ->addForeignKey('permission_id', 'permissions', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
                        ->create();
    }
}