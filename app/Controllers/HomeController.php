<?php

namespace TokoBot\Controllers;

use TokoBot\Helpers\Session;

class HomeController extends BaseController
{
    public function index()
    {
        // If user is already logged in, redirect to dashboard
        if (Session::get('user_role')) {
            header('Location: /dashboard');
            exit();
        }

        // For guests, show the landing page
        $dm = $this->container->get('template');

        // Muat view halaman landing yang mandiri
        require_once VIEWS_PATH . '/home_landing.php';
    }
}