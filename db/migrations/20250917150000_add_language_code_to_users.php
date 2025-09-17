<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddLanguageCodeToUsers extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('users');
        $table->addColumn('language_code', 'string', [
            'limit' => 10, // cth: 'en', 'id', 'en-US'
            'null' => true,
            'default' => null,
            'after' => 'photo_url'
        ])->update();
    }
}
