<?php

namespace TokoBot\Controllers;

class AdminController extends DashmixController
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
        // Changed to use Dashmix dashboard
        $this->dashmixDashboard();
    }

    // New method for Dashmix Admin Dashboard
    public function dashmixDashboard()
    {
        $adminNav = [
            [
                'name' => 'Dashboard',
                'icon' => 'fa fa-location-arrow',
                'url' => '/admin/dashboard',
                'active' => true
            ],
            [
                'name' => 'Analytics',
                'icon' => 'fa fa-chart-line',
                'url' => '/admin/analytics' // Assuming an analytics page
            ],
            [
                'heading' => 'Manage'
            ],
            [
                'name' => 'Users',
                'icon' => 'fa fa-users',
                'submenu' => [
                    [
                        'name' => 'View Users',
                        'url' => '/admin/users'
                    ],
                    [
                        'name' => 'Add User',
                        'url' => '/admin/users/add' // Assuming an add user page
                    ]
                ]
            ],
            [
                'name' => 'Settings',
                'icon' => 'fa fa-cog',
                'url' => '/admin/settings'
            ],
            [
                'name' => 'Reports',
                'icon' => 'fa fa-file-alt',
                'url' => '/admin/reports'
            ]
        ];

        $this->renderDashmix(
            __DIR__ . '/../../views/admin/dashboard.php',
            'Admin Dashboard',
            'Welcome to the admin dashboard.',
            $adminNav
        );
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