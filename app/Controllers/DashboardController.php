<?php

namespace TokoBot\Controllers;

class DashboardController
{
    public function index()
    {
        $contentView = __DIR__ . '/../../views/dashboard.php';
        require_once __DIR__ . '/../../views/templates/member_base.php';
    }
}
