<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RemoveLoginTokenFromUsersTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('users');
        $table->removeColumn('login_token')
              ->update();
    }
}