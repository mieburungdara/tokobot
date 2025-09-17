<?php

namespace TokoBot\Controllers;

use TokoBot\Helpers\Logger;
use TokoBot\Helpers\Session;

class MiniAppController extends DashmixController
{
    /**
     * Menampilkan halaman utama Mini App setelah memverifikasi bot_id.
     * Ini adalah entry point yang akan diakses oleh Telegram.
     * @param int $bot_id ID bot yang didapat dari URL.
     */
    public function index(int $bot_id)
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
            $sql = "INSERT INTO users (telegram_id, username, first_name, last_name, language_code, is_premium, last_activity_at) "
                 . "VALUES (?, ?, ?, ?, ?, ?, NOW()) "
                 . "ON DUPLICATE KEY UPDATE username = VALUES(username), "
                 . "first_name = VALUES(first_name), last_name = VALUES(last_name), "
                 . "language_code = VALUES(language_code), is_premium = VALUES(is_premium), "
                 . "last_activity_at = NOW()";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $user['id'],
                $user['username'] ?? null,
                $user['first_name'],
                $user['last_name'] ?? null,
                $user['language_code'] ?? 'en',
                isset($user['is_premium']) && $user['is_premium'] ? 1 : 0,
            ]);
        } catch (\Exception $e) {
            Logger::channel('database')->error('Failed to sync Mini App user.', ['error' => $e->getMessage()]);
            // Jangan hentikan proses jika DB gagal, tapi catat errornya.
        }
        
        // Set session jika perlu
        Session::set('user_id', $user['id']);
        Session::set('user_role', 'member'); // Atau ambil dari DB jika sudah ada role

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
