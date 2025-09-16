<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddLastActivityToUsers extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('users');
        $table->addColumn('last_activity_at', 'timestamp', [
            'null' => true,
            'default' => null,
            'after' => 'updated_at',
        ])
        ->update();
    }
}