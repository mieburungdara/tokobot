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

require_once ROOT_PATH . '/vendor/autoload.php';

use TokoBot\Core\Container;
use TokoBot\Helpers\Session;
use TokoBot\Helpers\Logger;

// --- Start DI Container Setup ---

// Require the Template class since it's not namespaced and autoloaded
require_once VIEWS_PATH . '/inc/_classes/Template.php';

$container = new Container();

// Create the template object
$dm = new Template('Dashmix', '5.10', 'assets');

// Load template configuration
$templateConfig = require_once CONFIG_PATH . '/template.php';

// Apply configuration to the template object
foreach ($templateConfig as $key => $value) {
    $dm->$key = $value;
}

// Store the configured template object in the container
$container->set('template', $dm);

// --- End DI Container Setup ---


// Set global error and exception handlers
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new \ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function ($exception) {
    Logger::channel('critical')->critical(
        $exception->getMessage(),
        [
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]
    );

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

// Fetch method and URI
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

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
        http_response_code(405);
        echo "405 Method Not Allowed";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        // Route Protection Middleware
        $handlerKey = is_array($handler) ? $handler[0] . '@' . $handler[1] : null;
        if (isset($handlerRoles[$handlerKey])) {
            $allowedRoles = $handlerRoles[$handlerKey];
            $userRole = \TokoBot\Helpers\Session::get('user_role', 'guest');
            if (!in_array($userRole, $allowedRoles)) {
                $errorController = new \TokoBot\Controllers\ErrorController();
                $errorController->forbidden();
                exit();
            }
        }

        // Call the handler
        if (is_array($handler) && count($handler) === 2) {
            $controllerClass = $handler[0];
            $method = $handler[1];

            // *** Pass the container to the controller constructor ***
            $controller = new $controllerClass($container);

            call_user_func_array([$controller, $method], $vars);
        } else {
            // Handle closure or other callable
            // Closures won't have access to the container unless we bind it.
            // For now, we assume all routes use controllers.
            call_user_func_array($handler, $vars);
        }
        break;
}