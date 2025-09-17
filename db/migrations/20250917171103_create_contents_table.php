<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateContentsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('contents');
        $table->addColumn('content_uid', 'string', ['limit' => 15])
              ->addColumn('seller_telegram_id', 'biginteger')
              ->addColumn('price', 'decimal', ['precision' => 15, 'scale' => 2])
              ->addColumn('status', 'string', ['limit' => 25, 'default' => 'pending_confirmation'])
              ->addTimestamps()
              ->addIndex(['content_uid'], ['unique' => true])
              ->addIndex(['seller_telegram_id'])
              ->create();
    }
}