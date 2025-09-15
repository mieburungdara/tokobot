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
        $layoutStart = __DIR__ . '/../../views/templates/member_layout_start.php';
        $layoutEnd = __DIR__ . '/../../views/templates/member_layout_end.php';
        $this->render($contentView, $pageTitle, $layoutStart, $layoutEnd);
    }

    // Tambahkan metode lain yang relevan untuk fungsionalitas member di sini
    // public function profile() { ... }
    // public function settings() { ... }
}