<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddTokenToTbots extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('tbots');
        $table->addColumn('token', 'text', [
            'null' => true, // Nullable karena mungkin bot ditambahkan tanpa token awal
            'default' => null,
            'after' => 'is_bot'
        ])->update();
    }
}
