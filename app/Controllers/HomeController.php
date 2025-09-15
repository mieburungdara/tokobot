<?php

namespace TokoBot\Controllers;

class HomeController
{
    public function index()
    {
        require_once __DIR__ . '/../../views/templates/head.php';
        require_once __DIR__ . '/../../views/home.php';
        require_once __DIR__ . '/../../views/templates/foot.php';
    }
}
