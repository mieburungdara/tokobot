<?php

namespace TokoBot\Controllers;

class ErrorController extends DashmixController
{
    public function forbidden()
    {
        http_response_code(403);
        $this->renderDashmix(
            VIEWS_PATH . '/errors/403.php',
            '403 Access Denied'
        );
    }
}
