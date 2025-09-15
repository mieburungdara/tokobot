<?php

namespace TokoBot\Controllers;

class AdminController
{
    public function index()
    {
        $contentView = __DIR__ . '/../../views/admin.php';
        require_once __DIR__ . '/../../views/templates/admin_base.php';
    }

    public function users()
    {
        $contentView = __DIR__ . '/../../views/admin/users.php';
        require_once __DIR__ . '/../../views/templates/admin_base.php';
    }

    public function settings()
    {
        $contentView = __DIR__ . '/../../views/admin/settings.php';
        require_once __DIR__ . '/../../views/templates/admin_base.php';
    }

    public function reports()
    {
        $contentView = __DIR__ . '/../../views/admin/reports.php';
        require_once __DIR__ . '/../../views/templates/admin_base.php';
    }
}
