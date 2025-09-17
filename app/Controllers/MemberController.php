<?php

namespace TokoBot\Controllers;

class MemberController extends DashmixController
{
    /**
     * Menampilkan dashboard utama untuk member.
     */
    public function index()
    {
        $breadcrumbs = [
            ['name' => 'Dashboard']
        ];

        $this->renderDashmix(
            VIEWS_PATH . '/member/dashboard.php',
            'Member Dashboard',
            'Welcome to your member dashboard.',
            [], // Gunakan navigasi default
            $breadcrumbs
        );
    }
}
