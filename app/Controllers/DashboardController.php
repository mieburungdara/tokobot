<?php

namespace TokoBot\Controllers;

class DashboardController
{
    public function index()
    {
        require_once __DIR__ . '/../../views/templates/header.php';
        require_once __DIR__ . '/../../views/dashboard.php';
        require_once __DIR__ . '/../../views/templates/footer.php';
    }
}
