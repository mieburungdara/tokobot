<?php

namespace TokoBot\Controllers;

class MemberController extends BaseController
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

    // Tambahkan metode lain yang relevan untuk fungsionalitas member di sini
    // public function profile() { ... }
    // public function settings() { ... }
}