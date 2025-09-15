<?php

namespace TokoBot\Controllers;

class ErrorController extends DashmixController
{
    public function forbidden()
    {
        http_response_code(403);

        $breadcrumbs = [
            ['name' => 'Error'],
            ['name' => '403']
        ];

        $this->renderDashmix(
            VIEWS_PATH . '/errors/403.php',
            'Access Denied', // Judul halaman
            '',
            [],
            $breadcrumbs
        );
    }

    public function notFound()
    {
        http_response_code(404);

        $breadcrumbs = [
            ['name' => 'Error'],
            ['name' => '404']
        ];

        $this->renderDashmix(
            VIEWS_PATH . '/errors/404.php',
            'Page Not Found', // Judul halaman
            '',
            [],
            $breadcrumbs
        );
    }
}