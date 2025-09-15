<?php

namespace TokoBot\Controllers\Admin;

class AdminController
{
    public function index()
    {
        $contentView = __DIR__ . '/../../views/admin.php';
        require_once __DIR__ . '/../../views/templates/admin_base.php';
    }
}
