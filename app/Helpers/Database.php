<?php

namespace TokoBot\Helpers;

class Database
{
    /**
     * @var \PDO|null Satu-satunya instance koneksi PDO.
     */
    private static ?\PDO $instance = null;

    /**
     * Constructor dibuat private untuk mencegah pembuatan objek langsung.
     */
    private function __construct()
    {
    }

    /**
     * Mendapatkan satu-satunya instance koneksi database.
     *
     * @return \PDO
     */
    public static function getInstance(): \PDO
    {
        if (self::$instance === null) {
            $dbConfig = require CONFIG_PATH . '/database.php';

            $dsn = $dbConfig['dsn'];
            $user = $dbConfig['mysql']['username'];
            $pass = $dbConfig['mysql']['password'];

            try {
                self::$instance = new \PDO($dsn, $user, $pass);

                // Atribut PDO yang umum digunakan untuk keamanan dan efisiensi
                self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
                self::$instance->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            } catch (\PDOException $e) {
                // Sebaiknya error ini di-log, untuk sekarang kita hentikan saja
                die("Koneksi database gagal: " . $e->getMessage());
            }
        }

        return self::$instance;
    }

    /**
     * Mencegah instance di-clone.
     */
    private function __clone()
    {
    }

    /**
     * Mencegah instance di-unserialize.
     */
    public function __wakeup()
    {
    }
}
