<?php

namespace TokoBot\Controllers;

class ErrorController extends BaseController // Tidak perlu DashmixController lagi
{
    public function badRequest() // 400
    {
        http_response_code(400);
        require_once VIEWS_PATH . '/errors/400.php';
    }

    public function unauthorized() // 401
    {
        http_response_code(401);
        require_once VIEWS_PATH . '/errors/401.php';
    }

    public function forbidden() // 403
    {
        http_response_code(403);
        require_once VIEWS_PATH . '/errors/403.php';
    }

    public function notFound() // 404
    {
        http_response_code(404);
        require_once VIEWS_PATH . '/errors/404.php';
    }

    public function internalError() // 500
    {
        http_response_code(500);
        require_once VIEWS_PATH . '/errors/500.php';
    }

    public function serviceUnavailable() // 503
    {
        http_response_code(503);
        require_once VIEWS_PATH . '/errors/503.php';
    }
}
