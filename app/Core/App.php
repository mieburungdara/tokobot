<?php

namespace TokoBot\Core;

use TokoBot\Controllers\ErrorController;
use FastRoute\Dispatcher;

class App
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function run()
    {
        // Include routes and role definitions
        $dispatcher = require_once ROOT_PATH . '/routes.php';
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
            case Dispatcher::NOT_FOUND:
                $errorController = new ErrorController();
                $errorController->notFound();
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                http_response_code(405);
                echo "405 Method Not Allowed";
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                // Route Protection Middleware
                $handlerKey = is_array($handler) ? $handler[0] . '@' . $handler[1] : null;
                if (isset($handlerRoles[$handlerKey])) {
                    $allowedRoles = $handlerRoles[$handlerKey];
                    $userRole = \TokoBot\Helpers\Session::get('user_role', 'guest');
                    if (!in_array($userRole, $allowedRoles)) {
                        $errorController = new ErrorController();
                        $errorController->forbidden();
                        exit();
                    }
                }

                // Call the handler
                if (is_array($handler) && count($handler) === 2) {
                    $controllerClass = $handler[0];
                    $method = $handler[1];

                    $controller = new $controllerClass($this->container);

                    call_user_func_array([$controller, $method], $vars);
                } else {
                    // Handle closure or other callable
                    call_user_func_array($handler, $vars);
                }
                break;
        }
    }
}
