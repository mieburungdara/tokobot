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

    public function dashboard()
    {
        $pageTitle = "Admin Panel";
        $contentView = __DIR__ . '/../../views/admin/dashboard.php';
        $layoutStart = __DIR__ . '/../../views/templates/admin_layout_start.php';
        $layoutEnd = __DIR__ . '/../../views/templates/admin_layout_end.php';
        $this->render($contentView, $pageTitle, $layoutStart, $layoutEnd);
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