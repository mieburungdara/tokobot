<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddRememberTokenToUsers extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('users');
        $table->addColumn('remember_selector', 'string', ['limit' => 255, 'null' => true, 'after' => 'password'])
              ->addColumn('remember_validator_hash', 'string', ['limit' => 255, 'null' => true, 'after' => 'remember_selector'])
              ->addIndex(['remember_selector'], ['unique' => true])
              ->save();
    }
}