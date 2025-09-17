<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddUpdateIdToMessagesTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('messages');

        // Modify 'id' to be auto-increment and ensure it's the primary key
        $table->changeColumn('id', 'biginteger', ['identity' => true, 'signed' => false, 'comment' => 'Auto-increment primary key'])
              ->update();

        // Add new 'update_id' column
        $table->addColumn('update_id', 'biginteger', ['after' => 'id', 'null' => false, 'signed' => false, 'comment' => 'Update ID from Telegram'])
              ->addIndex('update_id', ['unique' => true]) // Add unique index to update_id
              ->update();
    }
}
