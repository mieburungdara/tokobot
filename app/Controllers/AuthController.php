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
            Session::set('user_id', 1); // Dummy user ID for xoradmin
            Session::set('user_role', 'admin');
            Session::set('auth_source', 'xoradmin'); // Set authentication source
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

    
}
