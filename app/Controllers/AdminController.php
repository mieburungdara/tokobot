<?php

namespace TokoBot\Controllers;

class AdminController extends DashmixController
{
    // Mengarahkan method index ke dashboard utama
    public function index()
    {
        $this->dashmixDashboard();
    }

    public function dashmixDashboard()
    {
        // Breadcrumb untuk halaman dashboard
        $breadcrumbs = [
            ['name' => 'Dashboard']
        ];

        // Menggunakan konstanta VIEWS_PATH untuk path yang lebih bersih
        $this->renderDashmix(
            VIEWS_PATH . '/admin/dashboard.php',
            'Admin Dashboard',
            'Welcome to the admin dashboard.',
            [], // Menggunakan navigasi default dari config
            $breadcrumbs
        );
    }

    public function users()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Users']
        ];

        $this->renderDashmix(
            VIEWS_PATH . '/admin/users.php',
            'User Management',
            'Manage all application users.',
            [], // Menggunakan navigasi default dari config
            $breadcrumbs
        );
    }

    public function settings()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Settings']
        ];

        $this->renderDashmix(
            VIEWS_PATH . '/admin/settings.php',
            'Application Settings',
            'Configure application settings.',
            [], // Menggunakan navigasi default dari config
            $breadcrumbs
        );
    }

    public function reports()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
            ['name' => 'Reports']
        ];

        $this->renderDashmix(
            VIEWS_PATH . '/admin/reports.php',
            'Reports',
            'View application reports.',
            [], // Menggunakan navigasi default dari config
            $breadcrumbs
        );
    }
}
