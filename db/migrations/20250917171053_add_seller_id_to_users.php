<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddSellerIdToUsers extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('users');
        $table->addColumn('seller_id', 'string', ['limit' => 5, 'null' => true, 'after' => 'last_name'])
              ->addIndex(['seller_id'], ['unique' => true])
              ->update();
    }
}