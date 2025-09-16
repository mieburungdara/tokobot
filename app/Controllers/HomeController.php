<?php

namespace TokoBot\Controllers;

class HomeController extends BaseController
{
    public function index()
    {
        global $dm; // Make the global $dm object available

        // Muat view halaman landing yang mandiri
        require_once VIEWS_PATH . '/home_landing.php';
    }
}
