<?php

namespace TokoBot\Controllers;

class HomeController
{
    public function index()
    {
        require_once __DIR__ . '/../../views/templates/header.php';
        require_once __DIR__ . '/../../views/home.php';
        require_once __DIR__ . '/../../views/templates/footer.php';
    }
}
