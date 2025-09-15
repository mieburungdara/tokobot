<?php

namespace TokoBot\Controllers;

class MemberController
{
    public function index()
    {
        // Logika untuk halaman member akan ditempatkan di sini
        // Contoh: menampilkan daftar member, profil member, dll.
        require_once __DIR__ . '/../../views/templates/header.php';
        require_once __DIR__ . '/../../views/member.php'; // Asumsi ada view member.php
        require_once __DIR__ . '/../../views/templates/footer.php';
    }

    // Tambahkan metode lain yang relevan untuk fungsionalitas member di sini
    // public function profile() { ... }
    // public function settings() { ... }
}
