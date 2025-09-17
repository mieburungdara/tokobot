<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateBotStorageChannelsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('bot_storage_channels');
        $table->addColumn('bot_id', 'biginteger')
              ->addColumn('channel_id', 'biginteger')
              ->addColumn('last_used_at', 'timestamp', ['null' => true])
              ->addIndex(['bot_id'])
              ->create();
    }
}