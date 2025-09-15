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
        Session::destroy();
        header('Location: /xoradmin');
        exit();
    }
}
