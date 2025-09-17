<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUserStatesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('user_states', ['id' => false, 'primary_key' => ['telegram_id']]);
        $table->addColumn('telegram_id', 'biginteger')
              ->addColumn('state', 'string', ['limit' => 50])
              ->addColumn('context', 'json', ['null' => true])
              ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
              ->create();
    }
}