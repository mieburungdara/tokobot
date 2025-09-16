<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED);

// Define global path constants
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Load the template configuration globally so $dm is available everywhere
require_once ROOT_PATH . '/views/inc/_global/config.php';
require_once ROOT_PATH . '/views/inc/backend/config.php';

require_once ROOT_PATH . '/vendor/autoload.php';

use TokoBot\Helpers\Session;
use TokoBot\Helpers\Logger;

// Set global error and exception handlers
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        // This error code is not included in error_reporting
        return;
    }
    throw new \ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function ($exception) {
    // Log the exception to its own channel
    Logger::channel('critical')->critical(
        $exception->getMessage(),
        [
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]
    );

    // Show a generic error page to the user
    // Avoid showing raw errors in production
    if (!headers_sent()) {
        $errorController = new \TokoBot\Controllers\ErrorController();
        $errorController->internalError();
    }
});


Session::start();

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

// Include routes and role definitions
require_once ROOT_PATH . '/routes.php';
$handlerRoles = require_once CONFIG_PATH . '/roles.php';

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        $errorController = new \TokoBot\Controllers\ErrorController();
        $errorController->notFound();
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        http_response_code(405);
        echo "405 Method Not Allowed";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        // --- START: Route Protection Middleware ---
        $handlerKey = null;
        if (is_array($handler) && count($handler) === 2) {
            $handlerKey = $handler[0] . '@' . $handler[1];
        }

        // Check if the current route's handler is in our roles map.
        if (isset($handlerRoles[$handlerKey])) {
            $allowedRoles = $handlerRoles[$handlerKey];
            $userRole = \TokoBot\Helpers\Session::get('user_role', 'guest'); // Default to 'guest' if not logged in

            // If the user's role is not in the list of allowed roles, deny access.
            if (!in_array($userRole, $allowedRoles)) {
                // Panggil ErrorController untuk menampilkan halaman 403 yang sudah di-desain.
                $errorController = new \TokoBot\Controllers\ErrorController();
                $errorController->forbidden();
                exit();
            }
        }
        // --- END: Route Protection Middleware ---

        // Call the handler
        if (is_array($handler) && count($handler) === 2) {
            $controllerClass = $handler[0];
            $method = $handler[1];
            $controller = new $controllerClass();
            call_user_func_array([$controller, $method], $vars);
        } else {
            // Handle closure or other callable
            call_user_func_array($handler, $vars);
        }
        break;
}

