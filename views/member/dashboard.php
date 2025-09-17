<?php
// views/member/dashboard.php

// Pastikan hanya diakses melalui controller
if (!defined('APP_RUNNING')) {
    header('HTTP/1.1 403 Forbidden');
    exit('No direct script access allowed');
}

// Contoh data yang bisa dilewatkan dari controller
$pageTitle = $data['pageTitle'] ?? 'Member Dashboard';
$pageDescription = $data['pageDescription'] ?? 'Welcome to your member dashboard.';

// Sertakan header atau layout dasar jika ada
// require_once VIEWS_PATH . '/templates/header.php';
?>

<!-- Content Wrapper -->
<div id="main-container">
    <!-- Page Content -->
    <div class="content">
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title"><?= htmlspecialchars($pageTitle) ?></h3>
            </div>
            <div class="block-content">
                <p><?= htmlspecialchars($pageDescription) ?></p>
                <p>This is your member dashboard. More content will be added here soon!</p>
                <!-- Contoh: Menampilkan nama pengguna jika tersedia -->
                <?php if (isset($data['user_name'])): ?>
                    <p>Hello, <?= htmlspecialchars($data['user_name']) ?>!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- END Page Content -->
</div>
<!-- END Content Wrapper -->

<?php
// Sertakan footer atau layout dasar jika ada
// require_once VIEWS_PATH . '/templates/footer.php';
?>