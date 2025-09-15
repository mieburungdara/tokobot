<?php

namespace TokoBot\Controllers;

class AdminController extends BaseController
{
    public function index()
    {
        $pageTitle = "Admin Panel";
        $contentView = __DIR__ . '/../../views/admin.php';
        require_once __DIR__ . '/../../views/templates/head.php';
        $this->render($contentView, $pageTitle);
        require_once __DIR__ . '/../../views/templates/foot.php';
    }

    protected function renderModernize($contentView, $pageTitle)
    {
        // Define BASE_URL if it's not already defined (assuming it's defined globally)
        if (!defined('BASE_URL')) {
            define('BASE_URL', ''); // Adjust this if your BASE_URL is different
        }
        require_once __DIR__ . '/../../views/templates/admin_modernize_layout.php';
    }

    public function dashboard()
    {
        $pageTitle = "Admin Dashboard";
        $contentView = __DIR__ . '/../../views/admin/dashboard_modernize.php';
        $this->renderModernize($contentView, $pageTitle);
    }

    public function users()
    {
        $pageTitle = "Admin Panel";
        $contentView = __DIR__ . '/../../views/admin/users.php';
        require_once __DIR__ . '/../../views/templates/head.php';
        $this->render($contentView, $pageTitle);
        require_once __DIR__ . '/../../views/templates/foot.php';
    }

    public function settings()
    {
        $pageTitle = "Admin Panel";
        $contentView = __DIR__ . '/../../views/admin/settings.php';
        require_once __DIR__ . '/../../views/templates/head.php';
        $this->render($contentView, $pageTitle);
        require_once __DIR__ . '/../../views/templates/foot.php';
    }

    public function reports()
    {
        $pageTitle = "Admin Panel";
        $contentView = __DIR__ . '/../../views/admin/reports.php';
        require_once __DIR__ . '/../../views/templates/head.php';
        $this->render($contentView, $pageTitle);
        require_once __DIR__ . '/../../views/templates/foot.php';
    }
}