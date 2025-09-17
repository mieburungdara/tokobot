<?php

namespace TokoBot\Controllers;

use TokoBot\Helpers\Logger;
use TokoBot\Helpers\Session;

class MiniAppController extends DashmixController
{
    /**
     * Halaman verifikasi awal untuk Mini App.
     * @param int $bot_id ID bot yang didapat dari URL.
     */
    public function start(int $bot_id)
    {
        // Verifikasi apakah bot ada dan punya token.
        $botsFile = CONFIG_PATH . '/tbots.php';
        $botTokens = file_exists($botsFile) ? require $botsFile : [];

        if (!isset($botTokens[$bot_id])) {
            // Jika bot tidak ditemukan, tampilkan halaman error.
            http_response_code(404);
            require_once VIEWS_PATH . '/miniapp/invalid_bot.php';
            return;
        }

        // Jika bot valid, render halaman start yang akan me-redirect ke halaman app.
        $this->renderDashmix(
            VIEWS_PATH . '/miniapp/start.php',
            'Verifying...',
            '',
            [],
            [],
            ['bot_id' => $bot_id],
            VIEWS_PATH . '/templates/miniapp_layout.php' // Gunakan layout minimalis untuk halaman redirect
        );
    }

    /**
     * Menampilkan halaman utama Mini App setelah verifikasi.
     * @param int $bot_id ID bot yang didapat dari URL.
     */
    public function app(int $bot_id)
    {
        // Langkah 1: Verifikasi apakah bot ada dan punya token.
        $botsFile = CONFIG_PATH . '/tbots.php';
        $botTokens = file_exists($botsFile) ? require $botsFile : [];

        if (!isset($botTokens[$bot_id])) {
            // Jika bot tidak ditemukan, tampilkan halaman error.
            http_response_code(404);
            require_once VIEWS_PATH . '/miniapp/invalid_bot.php';
            return;
        }

        // Langkah 2: Jika bot valid, siapkan template dan render.
        $dm = $this->container->get('template');
        $dm->page_scripts = ['https://telegram.org/js/telegram-web-app.js'];

        $this->renderDashmix(
            VIEWS_PATH . '/miniapp/index.php',      // File konten
            'TokoBot Mini App',                   // Judul Halaman
            'Welcome to TokoBot Mini App',        // Deskripsi Halaman
            [],                                   // Navigasi (gunakan default)
            [['name' => 'Mini App']],             // Breadcrumbs
            ['bot_id' => $bot_id]                   // Data yang akan di-pass ke view
        );
    }

    /**
     * Endpoint API untuk otentikasi pengguna Mini App.
     * Menerima initData dari frontend, memvalidasinya, dan mengembalikan data user.
     */
    public function authenticate()
    {
        header('Content-Type: application/json');

        // Ambil data dari body request POST (sekarang dalam format JSON)
        $requestBody = json_decode(file_get_contents('php://input'), true);
        $initDataString = $requestBody['initData'] ?? '';
        $bot_id = $requestBody['bot_id'] ?? null;

        if (empty($initDataString) || empty($bot_id)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'initData or bot_id is missing.']);
            return;
        }

        // Ambil token bot spesifik dari file konfigurasi.
        $botsFile = CONFIG_PATH . '/tbots.php';
        $botTokens = file_exists($botsFile) ? require $botsFile : [];
        $botToken = $botTokens[$bot_id] ?? null;

        if (!$botToken) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Bot configuration not found.']);
            return;
        }

        // Lakukan validasi hash dengan token yang spesifik
        $validated = $this->validateHash($initDataString, $botToken);

        if (!$validated) {
            Logger::channel('auth')->warning('Invalid Mini App hash.', ['initData' => $initDataString, 'bot_id' => $bot_id]);
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Authentication failed: Invalid hash.']);
            return;
        }

        // Jika valid, proses data pengguna
        parse_str($initDataString, $initData);
        $user = json_decode($initData['user'], true);

        // Sinkronisasi data pengguna ke database (opsional, tapi sangat direkomendasikan)
        try {
            $pdo = \TokoBot\Helpers\Database::getInstance();
            $sql = "INSERT INTO users (telegram_id, username, first_name, last_name, photo_url, language_code, is_premium, last_activity_at) "
                 . "VALUES (?, ?, ?, ?, ?, ?, ?, NOW()) "
                 . "ON DUPLICATE KEY UPDATE username = VALUES(username), "
                 . "first_name = VALUES(first_name), last_name = VALUES(last_name), photo_url = VALUES(photo_url), "
                 . "language_code = VALUES(language_code), is_premium = VALUES(is_premium), "
                 . "last_activity_at = NOW()";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $user['id'],
                $user['username'] ?? null,
                $user['first_name'],
                $user['last_name'] ?? null,
                $user['photo_url'] ?? null,
                $user['language_code'] ?? 'en',
                isset($user['is_premium']) && $user['is_premium'] ? 1 : 0,
            ]);

            // Catat interaksi di tabel relasi bot_user. Cukup masukkan data baru, 
            // atau biarkan trigger `ON UPDATE CURRENT_TIMESTAMP` pada `last_accessed_at` bekerja jika data sudah ada.
            $sqlBotUser = "INSERT INTO bot_user (bot_id, user_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE last_accessed_at = NOW()";
            $stmtBotUser = $pdo->prepare($sqlBotUser);
            $stmtBotUser->execute([$bot_id, $user['id']]);

            // Setelah sinkronisasi, ambil data lengkap pengguna dari DB untuk membuat sesi
            $stmt = $pdo->prepare("SELECT * FROM users WHERE telegram_id = ?");
            $stmt->execute([$user['id']]);
            $dbUser = $stmt->fetch();

            if ($dbUser) {
                // Buat sesi lengkap untuk pengguna
                Session::set('user', $dbUser); // Simpan semua data pengguna dalam satu array
                Session::set('user_id', $dbUser['telegram_id']);
                Session::set('user_role', $dbUser['role']); // Gunakan role dari database
                Logger::channel('auth')->info('Session created for Mini App user.', ['user_id' => $user['id'], 'role' => $dbUser['role']]);
            } else {
                // Ini seharusnya tidak terjadi jika sinkronisasi berhasil
                Logger::channel('auth')->error('Failed to fetch user from DB after Mini App sync.', ['user_id' => $user['id']]);
            }

        } catch (\Exception $e) {
            Logger::channel('database')->error('Failed to sync Mini App user.', ['error' => $e->getMessage()]);
            // Jangan hentikan proses jika DB gagal, tapi catat errornya.
        }

        // Kirim kembali data user sebagai konfirmasi
        echo json_encode([
            'status' => 'success',
            'message' => 'User authenticated successfully.',
            'user_data' => $user
        ]);
    }

    /**
     * Memvalidasi hash dari initData Telegram.
     * @param string $initDataString String initData lengkap (raw).
     * @param string $botToken Token rahasia bot.
     * @return bool
     */
    private function validateHash(string $initDataString, string $botToken): bool
    {
        $params = [];
        parse_str($initDataString, $params);

        if (!isset($params['hash'])) {
            return false;
        }

        $hash = $params['hash'];
        unset($params['hash']);

        ksort($params);

        $dataCheckString = implode("\n", array_map(function ($key, $value) {
            return "$key=$value";
        }, array_keys($params), $params));

        $secretKey = hash_hmac('sha256', $botToken, "WebAppData", true);
        $calculatedHash = hash_hmac('sha256', $dataCheckString, $secretKey);

        return $calculatedHash === $hash;
    }
}
