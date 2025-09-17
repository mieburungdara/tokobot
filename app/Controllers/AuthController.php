<?php

namespace TokoBot\Controllers;

use TokoBot\Helpers\Session;
use TokoBot\Helpers\Logger;

class AuthController extends BaseController
{
    public function showLoginForm($error = null)
    {
        // Render halaman login, teruskan pesan error jika ada
        require_once VIEWS_PATH . '/auth/login.php';
    }

    public function handleLogin()
    {
        $password = $_POST['password'] ?? '';
        $hardcodedPassword = 'sup3r4dmin'; // Password sementara

        $hashedPassword = password_hash($hardcodedPassword, PASSWORD_DEFAULT);
        if (password_verify($password, $hashedPassword)) { // Verifikasi password dengan aman
            // Password benar, atur session dan alihkan ke dashboard
            Session::set('user_role', 'admin');
            header('Location: /dashboard');
            exit();
        } else {
            // Password salah, log percobaan dan tampilkan form lagi
            Logger::channel('auth')->warning('Failed admin login attempt', [
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            $error = 'Password yang Anda masukkan salah.';
            $this->showLoginForm($error);
        }
    }

    public function logout()
    {
        Session::clear();
        header('Location: /xoradmin');
        exit();
    }

    public function handleTokenLogin($token)
    {
                $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        Logger::channel('auth')->info('Token login process started.', [
            'ip_address' => $ipAddress
        ]);


        try {
            $tokenHash = hash('sha256', $token);
            Logger::channel('auth')->debug('Hashed received token for DB lookup.', [
                'token_hash' => $tokenHash,
                'ip_address' => $ipAddress
            ]);

            $pdo = \TokoBot\Helpers\Database::getInstance();

            // Cari pengguna dengan token yang cocok
            $sql = "SELECT * FROM users WHERE login_token = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$tokenHash]);
            $user = $stmt->fetch();
            Logger::channel('auth')->debug('DB query for token executed.', [
                'ip_address' => $ipAddress
            ]);


            if ($user) {
                Logger::channel('auth')->debug('Session set for user.', [
                    'user_id' => $user['telegram_id'],
                    'role' => $user['role'],
                    'ip_address' => $ipAddress
                ]);
                // Token valid, loginkan pengguna
                Session::set('user_id', $user['telegram_id']);
                Session::set('user_role', $user['role']);

                Logger::channel('auth')->info('Successful token login', [
                    'user_id' => $user['telegram_id'],
                    'ip_address' => $ipAddress
                ]);

                // Hapus token agar tidak bisa digunakan lagi (single-use)
                $sql = "UPDATE users SET login_token = NULL WHERE telegram_id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$user['telegram_id']]);
                
                Logger::channel('auth')->info('Token invalidated after use.', [
                    'user_id' => $user['telegram_id'],
                    'ip_address' => $ipAddress
                ]);

                Logger::channel('auth')->info('Successful token login. Redirecting to dashboard.', [
                    'user_id' => $user['telegram_id'],
                    'ip_address' => $ipAddress
                ]);

                // Send success message back to the user via Telegram
                $botId = $_GET['bot_id'] ?? null;
                if ($botId) {
                    $botToken = \TokoBot\Models\Bot::findTokenById((int)$botId);

                    if ($botToken) {
                        new \TelegramBot\Telegram($botToken);
                        \TelegramBot\Request::sendMessage([
                            'chat_id' => $user['telegram_id'],
                            'text' => "Login Anda berhasil dari IP: {\$ipAddress}! Anda sekarang dapat mengakses dashboard."
                        ]);
                        Logger::channel('auth')->info("Sent login success message to user: {$user['telegram_id']} via bot: {$botId}");
                    } else {
                        Logger::channel('auth')->warning("Could not send login success message: Bot token not found for bot ID: {$botId}");
                    }
                } else {
                    Logger::channel('auth')->warning("Could not send login success message: Bot ID not provided in URL.");
                }

                header('Location: /dashboard');
                exit();
            } else {
                // Token tidak valid
                Logger::channel('auth')->warning('Failed token login attempt', [
                    'token' => $token,
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);

                $errorController = new ErrorController();
                $errorController->unauthorized(); // Tampilkan halaman error 401
                exit();
            }
        } catch (\PDOException $e) {
            Logger::channel('database')->error('Token login database error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $errorController = new ErrorController();
            $errorController->internalError(); // Tampilkan halaman error 500
            exit();
        } catch (\Exception $e) {
            Logger::channel('app')->error('Token login unexpected error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $errorController = new ErrorController();
            $errorController->internalError(); // Tampilkan halaman error 500
            exit();
        }
    }
}
