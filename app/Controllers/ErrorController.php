<?php

namespace TokoBot\Controllers;

class ErrorController
{
    /**
     * Renders an error view within the standalone error layout.
     *
     * @param string $viewFile The name of the view file in the 'views/errors' directory.
     * @param int $statusCode The HTTP status code to set.
     */
    private function render(string $viewFile, int $statusCode)
    {
        http_response_code($statusCode);

        $viewPath = VIEWS_PATH . '/errors/' . $viewFile;

        if (file_exists($viewPath)) {
            // Capture the output of the specific error view
            ob_start();
            require $viewPath;
            $pageContent = ob_get_clean();

            // Include the main error layout, which will use $pageContent
            require_once VIEWS_PATH . '/templates/error_layout.php';
        } else {
            // Fallback if the view file doesn't exist
            echo "Error: View file not found for status code {$statusCode}.";
        }
    }

    public function badRequest() // 400
    {
        $this->render('400.php', 400);
    }

    public function unauthorized() // 401
    {
        $this->render('401.php', 401);
    }

    public function forbidden() // 403
    {
        $this->render('403.php', 403);
    }

    public function notFound() // 404
    {
        $this->render('404.php', 404);
    }

    public function internalError() // 500
    {
        $this->render('500.php', 500);
    }

    public function serviceUnavailable() // 503
    {
        $this->render('503.php', 503);
    }
}