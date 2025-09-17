<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateMediaTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('media');
        $table->addColumn('content_id', 'integer')
              ->addColumn('file_type', 'string', ['limit' => 20])
              ->addColumn('file_unique_id', 'string', ['limit' => 255])
              ->addColumn('file_size', 'integer', ['null' => true])
              ->addColumn('width', 'integer', ['null' => true])
              ->addColumn('height', 'integer', ['null' => true])
              ->addColumn('duration', 'integer', ['null' => true])
              ->addColumn('original_message_id', 'biginteger')
              ->addColumn('original_media_group_id', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('backup_channel_id', 'biginteger')
              ->addColumn('backup_message_id', 'biginteger')
              ->addColumn('raw_telegram_metadata', 'json')
              ->addIndex(['content_id'])
              ->addIndex(['file_unique_id'])
              ->addForeignKey('content_id', 'contents', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
              ->create();
    }
}