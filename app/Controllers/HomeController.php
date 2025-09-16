<?php

namespace TokoBot\Controllers;

class HomeController extends BaseController
{
    public function index()
    {
        $dm = $this->container->get('template');

        // Muat view halaman landing yang mandiri
        require_once VIEWS_PATH . '/home_landing.php';
    }
}
