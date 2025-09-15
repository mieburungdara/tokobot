<?php

namespace TokoBot\Controllers;

class MemberController extends DashmixController
{
    public function index()
    {
        // Logika untuk halaman member akan ditempatkan di sini
        // Contoh: menampilkan daftar member, profil member, dll.
        $pageTitle = "Member Dashboard";
        $contentView = __DIR__ . '/../../views/member.php';
        
        require_once __DIR__ . '/../../views/templates/head.php';
        $this->render($contentView, $pageTitle);
        require_once __DIR__ . '/../../views/templates/foot.php';
    }

    // New method for Dashmix Member Dashboard
    public function dashmixDashboard()
    {
        $memberNav = [
            [
                'name' => 'Dashboard',
                'icon' => 'fa fa-home',
                'url' => '/dashboard',
                'active' => true
            ],
            [
                'name' => 'Profile',
                'icon' => 'fa fa-user',
                'url' => '/member/profile' // Assuming a member profile page
            ],
            [
                'name' => 'Settings',
                'icon' => 'fa fa-cog',
                'url' => '/member/settings' // Assuming a member settings page
            ]
        ];

        $this->renderDashmix(
            __DIR__ . '/../../views/member/dashboard.php',
            'Member Dashboard',
            'Welcome to your member dashboard.',
            $memberNav
        );
    }

    // Tambahkan metode lain yang relevan untuk fungsionalitas member di sini
    // public function profile() { ... }
    // public function settings() { ... }
}