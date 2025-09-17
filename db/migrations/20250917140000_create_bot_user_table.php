<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateBotUserTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('bot_user', [
            'id' => false, // Tidak perlu auto-increment ID
            'primary_key' => ['bot_id', 'user_id'] // Composite primary key
        ]);

        $table->addColumn('bot_id', 'biginteger')
              ->addColumn('user_id', 'biginteger')
              ->addColumn('is_banned', 'boolean', [
                  'default' => false,
                  'comment' => 'Apakah user dibanned dari bot ini?'
              ])
              ->addColumn('last_accessed_at', 'timestamp', [
                  'default' => 'CURRENT_TIMESTAMP',
                  'update' => 'CURRENT_TIMESTAMP',
                  'comment' => 'Kapan terakhir user berinteraksi dengan bot ini'
              ])
              ->addForeignKey('bot_id', 'tbots', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
              ->addForeignKey('user_id', 'users', 'telegram_id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
              ->create();
    }
}
