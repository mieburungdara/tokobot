<?php

namespace TokoBot\Controllers;

class MemberController extends DashmixController
{
    /**
     * Mengalihkan /member ke dashboard utama member.
     */
    public function index()
    {
                $this->dashmixDashboard();
    }

    /**
     * Menampilkan dashboard utama untuk member.
     */
    public function dashmixDashboard()
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
