<?php

require_once __DIR__ . '/../vendor/autoload.php';

$page = $_GET['page'] ?? 'home';

$routes = [
    'home' => TokoBot\Controllers\HomeController::class,
    'admin' => TokoBot\Controllers\AdminController::class,
    'dashboard' => TokoBot\Controllers\DashboardController::class,
];

if (array_key_exists($page, $routes)) {
    $controllerClass = $routes[$page];
    $controller = new $controllerClass();
    $controller->index();
} else {
    http_response_code(404);
    echo "404 Not Found";
}
