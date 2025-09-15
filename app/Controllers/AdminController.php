<?php

namespace TokoBot\Controllers;

class AdminController extends BaseController
{
    public function index()
    {
        $pageTitle = "Admin Panel";
        $contentView = __DIR__ . '/../../views/admin.php';
        $layoutStart = __DIR__ . '/../../views/templates/admin_layout_start.php';
        $layoutEnd = __DIR__ . '/../../views/templates/admin_layout_end.php';
        $this->render($contentView, $pageTitle, $layoutStart, $layoutEnd);
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
        $layoutStart = __DIR__ . '/../../views/templates/admin_layout_start.php';
        $layoutEnd = __DIR__ . '/../../views/templates/admin_layout_end.php';
        $this->render($contentView, $pageTitle, $layoutStart, $layoutEnd);
    }

    public function settings()
    {
        $pageTitle = "Admin Panel";
        $contentView = __DIR__ . '/../../views/admin/settings.php';
        $layoutStart = __DIR__ . '/../../views/templates/admin_layout_start.php';
        $layoutEnd = __DIR__ . '/../../views/templates/admin_layout_end.php';
        $this->render($contentView, $pageTitle, $layoutStart, $layoutEnd);
    }

    public function reports()
    {
        $pageTitle = "Admin Panel";
        $contentView = __DIR__ . '/../../views/admin/reports.php';
        $layoutStart = __DIR__ . '/../../views/templates/admin_layout_start.php';
        $layoutEnd = __DIR__ . '/../../views/templates/admin_layout_end.php';
        $this->render($contentView, $pageTitle, $layoutStart, $layoutEnd);
    }
}