<?php

namespace TokoBot\Controllers;

use TokoBot\Helpers\Session;

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

        if (password_verify($password, password_hash($hardcodedPassword, PASSWORD_DEFAULT))) { // Verifikasi password dengan aman
            // Password benar, atur session dan alihkan ke dashboard
            Session::set('user_role', 'admin');
            header('Location: /dashboard');
            exit();
        } else {
            // Password salah, tampilkan lagi form login dengan pesan error
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
        $tokenHash = hash('sha256', $token);

        $pdo = \TokoBot\Helpers\Database::getInstance();

        // Cari pengguna dengan token yang cocok dan belum kedaluwarsa
        $sql = "SELECT * FROM users WHERE login_token = ? AND token_expires_at > NOW()";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tokenHash]);
        $user = $stmt->fetch();

        if ($user) {
            // Token valid, loginkan pengguna
            Session::set('user_id', $user['telegram_id']);
            Session::set('user_role', $user['role']);

            // Hapus token agar tidak bisa digunakan lagi (single-use)
            $sql = "UPDATE users SET login_token = NULL, token_expires_at = NULL WHERE telegram_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user['telegram_id']]);

            header('Location: /dashboard');
            exit();
        } else {
            // Token tidak valid atau sudah kedaluwarsa
            $errorController = new ErrorController();
            $errorController->unauthorized(); // Tampilkan halaman error 401
            exit();
        }
    }
}
