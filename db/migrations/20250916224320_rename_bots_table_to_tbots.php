<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RenameBotsTableToTbots extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('bots');
        $table->rename('tbots')
              ->update();
    }
}