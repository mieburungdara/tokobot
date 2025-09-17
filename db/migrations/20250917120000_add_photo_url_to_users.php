<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddPhotoUrlToUsers extends AbstractMigration
{
    public function change(): void
    {
        // Dapatkan tabel 'users'
        $table = $this->table('users');

        // Tambahkan kolom baru 'photo_url'
        $table->addColumn('photo_url', 'string', [
            'limit' => 255,       // URL bisa cukup panjang
            'null' => true,         // Izinkan null jika pengguna tidak punya foto
            'default' => null,
            'after' => 'last_name' // Letakkan kolom setelah 'last_name'
        ])->update();
    }
}
