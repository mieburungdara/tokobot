<?php
/**
 * miniapp_layout.php
 *
 * Layout minimalis khusus untuk halaman Telegram Mini App.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($dm->title) ?></title>
    
    <!-- Script Wajib untuk Telegram Mini App -->
    <script src="https://telegram.org/js/telegram-web-app.js"></script>

    <!-- Variabel dari PHP ke JS -->
    <script>
        // Menyediakan variabel global untuk data yang di-pass dari controller
        window.appData = <?= json_encode($data ?? []) ?>;
    </script>

    <style>
        /* Sedikit reset CSS dan styling dasar */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            padding: 15px;
            margin: 0;
            color: var(--tg-theme-text-color, #000000);
            background-color: var(--tg-theme-bg-color, #ffffff);
        }
    </style>
</head>
<body>

    <!-- Konten utama dari view akan dirender di sini -->
    <?= $page_content ?>

</body>
</html>
