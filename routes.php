<?php

// routes.php

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    // Home route
    $r->addRoute('GET', '/', ['TokoBot\\Controllers\\HomeController', 'index']);
    $r->addRoute('GET', '/home', ['TokoBot\\Controllers\\HomeController', 'index']);

    // Admin routes
    $r->addRoute('GET', '/admin', ['TokoBot\Controllers\Admin\AdminController', 'index']);
    $r->addRoute('GET', '/admin/users', ['TokoBot\Controllers\Admin\AdminController', 'users']);
    $r->addRoute('GET', '/admin/settings', ['TokoBot\Controllers\Admin\AdminController', 'settings']);
    $r->addRoute('GET', '/admin/reports', ['TokoBot\Controllers\Admin\AdminController', 'reports']);

    // Dashboard routes (assuming it's for members)
    $r->addRoute('GET', '/dashboard', ['TokoBot\\Controllers\\Member\\DashboardController', 'index']);

    // Member routes
    $r->addRoute('GET', '/member', ['TokoBot\\Controllers\\MemberController', 'index']);
    // Add more member routes here, e.g., $r->addRoute('GET', '/member/{id}', ['TokoBot\\Controllers\\MemberController', 'show']);
});
