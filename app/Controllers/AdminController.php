<?php

namespace TokoBot\Controllers;

class AdminController
{
    public function index()
    {
        $pageTitle = "Admin Panel";
        require_once __DIR__ . '/../../views/templates/head.php';
        require_once __DIR__ . '/../../views/templates/admin_layout_start.php';
        $contentView = __DIR__ . '/../../views/admin.php';
        require_once $contentView;
        require_once __DIR__ . '/../../views/templates/admin_layout_end.php';
        require_once __DIR__ . '/../../views/templates/foot.php';
    }

    public function dashboard()
    {
        $pageTitle = "Admin Panel";
        require_once __DIR__ . '/../../views/templates/head.php';
        require_once __DIR__ . '/../../views/templates/admin_layout_start.php';
        $contentView = __DIR__ . '/../../views/admin/dashboard.php';
        require_once $contentView;
        require_once __DIR__ . '/../../views/templates/admin_layout_end.php';
        require_once __DIR__ . '/../../views/templates/foot.php';
    }

    public function users()
    {
        $pageTitle = "Admin Panel";
        require_once __DIR__ . '/../../views/templates/head.php';
        require_once __DIR__ . '/../../views/templates/admin_layout_start.php';
        $contentView = __DIR__ . '/../../views/admin/users.php';
        require_once $contentView;
        require_once __DIR__ . '/../../views/templates/admin_layout_end.php';
        require_once __DIR__ . '/../../views/templates/foot.php';
    }

    public function settings()
    {
        $pageTitle = "Admin Panel";
        require_once __DIR__ . '/../../views/templates/head.php';
        require_once __DIR__ . '/../../views/templates/admin_layout_start.php';
        $contentView = __DIR__ . '/../../views/admin/settings.php';
        require_once $contentView;
        require_once __DIR__ . '/../../views/templates/admin_layout_end.php';
        require_once __DIR__ . '/../../views/templates/foot.php';
    }

    public function reports()
    {
        $pageTitle = "Admin Panel";
        require_once __DIR__ . '/../../views/templates/head.php';
        require_once __DIR__ . '/../../views/templates/admin_layout_start.php';
        $contentView = __DIR__ . '/../../views/admin/reports.php';
        require_once $contentView;
        require_once __DIR__ . '/../../views/templates/admin_layout_end.php';
        require_once __DIR__ . '/../../views/templates/foot.php';
    }
}
