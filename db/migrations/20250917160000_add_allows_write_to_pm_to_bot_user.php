<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddAllowsWriteToPmToBotUser extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('bot_user');
        $table->addColumn('allows_write_to_pm', 'boolean', [
            'default' => false,
            'comment' => 'Does the user allow the bot to write to them?',
            'after' => 'is_banned'
        ])->update();
    }
}
