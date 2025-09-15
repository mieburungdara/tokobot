<?php

namespace TokoBot\Controllers;

class AdminController
{
    public function index()
    {
        require_once __DIR__ . '/../../views/templates/header.php';
        require_once __DIR__ . '/../../views/admin.php';
        require_once __DIR__ . '/../../views/templates/footer.php';
    }
}
