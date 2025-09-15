<?php

namespace TokoBot\Controllers;

class MemberController
{
    public function index()
    {
        // Logika untuk halaman member akan ditempatkan di sini
        // Contoh: menampilkan daftar member, profil member, dll.
        $pageTitle = "Member Dashboard";
        require_once __DIR__ . '/../../views/templates/head.php';
        require_once __DIR__ . '/../../views/templates/member_layout_start.php';
        $contentView = __DIR__ . '/../../views/member.php';
        require_once $contentView;
        require_once __DIR__ . '/../../views/templates/member_layout_end.php';
        require_once __DIR__ . '/../../views/templates/foot.php';
    }

    // Tambahkan metode lain yang relevan untuk fungsionalitas member di sini
    // public function profile() { ... }
    // public function settings() { ... }
}
