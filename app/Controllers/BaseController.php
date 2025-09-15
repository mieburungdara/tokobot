<?php

namespace TokoBot\Controllers;

class BaseController
{
    protected function render($contentView, $pageTitle, $layoutStart = null, $layoutEnd = null)
    {
        require_once __DIR__ . '/../../views/templates/head.php';
        if ($layoutStart) {
            require_once $layoutStart;
        }
        require_once $contentView;
        if ($layoutEnd) {
            require_once $layoutEnd;
        }
        require_once __DIR__ . '/../../views/templates/foot.php';
    }
}
