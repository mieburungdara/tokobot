<?php

// routes.php

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    // Home route
    $r->addRoute('GET', '/', ['TokoBot\\Controllers\\HomeController', 'index']);
    $r->addRoute('GET', '/home', ['TokoBot\\Controllers\\HomeController', 'index']);

    // Main Dashboard (handled by the new DashboardController)
    $r->addRoute('GET', '/dashboard', ['TokoBot\Controllers\DashboardController', 'index']);

    // Other Admin routes (without '/admin' prefix)
    $r->addRoute('GET', '/users', ['TokoBot\Controllers\AdminController', 'users']);
    $r->addRoute('GET', '/settings', ['TokoBot\Controllers\AdminController', 'settings']);
    $r->addRoute('GET', '/reports', ['TokoBot\Controllers\AdminController', 'reports']);

    // Member routes
    $r->addRoute('GET', '/member', ['TokoBot\Controllers\MemberController', 'index']);
    
    // Add more member routes here, e.g., $r->addRoute('GET', '/member/{id}', ['TokoBot\Controllers\MemberController', 'show']);

    // Auth routes
    $r->addRoute('GET', '/xoradmin', ['TokoBot\Controllers\AuthController', 'showLoginForm']);
    $r->addRoute('POST', '/xoradmin', ['TokoBot\Controllers\AuthController', 'handleLogin']);
    $r->addRoute('GET', '/logout', ['TokoBot\Controllers\AuthController', 'logout']);

    // Static Pages
    $r->addRoute('GET', '/support', ['TokoBot\Controllers\PageController', 'support']);
    $r->addRoute('GET', '/contact', ['TokoBot\Controllers\PageController', 'contact']);
});