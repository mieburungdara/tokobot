<?php
// Halaman ini berfungsi sebagai gerbang verifikasi.
// Jika controller memutuskan bot valid, view ini akan dirender.
// JavaScript di bawah akan segera mengarahkan pengguna ke aplikasi utama.

// Ambil bot_id dari data yang di-pass oleh controller
$bot_id = $data['bot_id'] ?? null;
$app_url = '/miniapp/app/' . $bot_id;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifying...</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; color: var(--tg-theme-text-color, #000); background-color: var(--tg-theme-bg-color, #fff); }
        .container { text-align: center; }
        .spinner { border: 4px solid rgba(0,0,0,.1); width: 36px; height: 36px; border-radius: 50%; border-left-color: var(--tg-theme-button-color, #007bff); animation: spin 1s ease infinite; margin: 0 auto 20px auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="container">
        <div class="spinner"></div>
        <p>Verifikasi berhasil, mengarahkan ke aplikasi...</p>
    </div>

    <script>
        // Menggunakan window.location.replace agar halaman verifikasi ini tidak masuk ke dalam riwayat browser.
        // Pengguna tidak akan bisa kembali ke halaman ini dengan menekan tombol "back".
        window.location.replace('<?= addslashes($app_url) ?>');
    </script>
</body>
</html>
