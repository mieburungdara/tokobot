<?php

// routes.php

return FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
        // Home route
        $r->addRoute('GET', '/', ['TokoBot\\Controllers\\HomeController', 'index']);
        $r->addRoute('GET', '/home', ['TokoBot\\Controllers\\HomeController', 'index']);
    
        // Main Dashboard (handled by the new DashboardController)
        $r->addRoute('GET', '/dashboard', ['TokoBot\Controllers\DashboardController', 'index', ['middleware' => ['AuthMiddleware']]]);
    
        // Other Admin routes (without '/admin' prefix)
        $r->addRoute('GET', '/users', ['TokoBot\Controllers\AdminController', 'users', ['middleware' => [['RoleMiddleware', 'admin']]]]);
        $r->addRoute('GET', '/settings', ['TokoBot\Controllers\AdminController', 'settings', ['middleware' => [['RoleMiddleware', 'admin']]]]);
        $r->addRoute('GET', '/reports', ['TokoBot\Controllers\AdminController', 'reports', ['middleware' => [['RoleMiddleware', 'admin']]]]);
        $r->addRoute('GET', '/analytics', ['TokoBot\Controllers\AdminController', 'botAnalytics', ['middleware' => [['RoleMiddleware', 'admin']]]]);
        $r->addRoute('GET', '/logs', ['TokoBot\Controllers\AdminController', 'viewLogs', ['middleware' => [['RoleMiddleware', 'admin']]]]);
        $r->addRoute('GET', '/bot-management', ['TokoBot\Controllers\AdminController', 'manageBots', ['middleware' => [['RoleMiddleware', 'admin']]]]);
        $r->addRoute('POST', '/bot-management', ['TokoBot\Controllers\AdminController', 'addBot', ['middleware' => [['RoleMiddleware', 'admin']]]]);
        $r->addRoute('POST', '/bot-management/{id:\d+}/delete', ['TokoBot\Controllers\AdminController', 'deleteBot', ['middleware' => [['RoleMiddleware', 'admin']]]]);

        // Storage Channel Management
        $r->addRoute('GET', '/storage-channels', ['TokoBot\Controllers\AdminController', 'storageChannels', ['middleware' => [['RoleMiddleware', 'admin']]]]);
        $r->addRoute('GET', '/storage-channels/add', ['TokoBot\Controllers\AdminController', 'addStorageChannel', ['middleware' => [['RoleMiddleware', 'admin']]]]);
        $r->addRoute('POST', '/storage-channels/add', ['TokoBot\Controllers\AdminController', 'addStorageChannel', ['middleware' => [['RoleMiddleware', 'admin']]]]);
        $r->addRoute('GET', '/storage-channels/edit/{id:\d+}', ['TokoBot\Controllers\AdminController', 'editStorageChannel', ['middleware' => [['RoleMiddleware', 'admin']]]]);
        $r->addRoute('POST', '/storage-channels/edit/{id:\d+}', ['TokoBot\Controllers\AdminController', 'editStorageChannel', ['middleware' => [['RoleMiddleware', 'admin']]]]);
        $r->addRoute('POST', '/storage-channels/delete/{id:\d+}', ['TokoBot\Controllers\AdminController', 'deleteStorageChannel', ['middleware' => [['RoleMiddleware', 'admin']]]]);
    
        // Member routes
        $r->addRoute('GET', '/member', ['TokoBot\Controllers\MemberController', 'index', ['middleware' => ['AuthMiddleware']]]);
    
        // Add more member routes here, e.g.,
        // $r->addRoute('GET', '/member/{id}', ['TokoBot\Controllers\MemberController', 'show']);
    
        // Auth routes
        $r->addRoute('GET', '/xoradmin', ['TokoBot\Controllers\AuthController', 'showLoginForm']);
        $r->addRoute('POST', '/xoradmin', ['TokoBot\Controllers\AuthController', 'handleLogin']);
        $r->addRoute('GET', '/logout', ['TokoBot\Controllers\AuthController', 'logout']);
    
    
        // Static Pages
        $r->addRoute('GET', '/support', ['TokoBot\Controllers\PageController', 'support']);
        $r->addRoute('GET', '/contact', ['TokoBot\Controllers\PageController', 'contact']);
    
        // Mini App routes
        $r->addRoute('GET', '/miniapp/start/{bot_id:\d+}', ['TokoBot\Controllers\MiniAppController', 'start']);
        $r->addRoute('GET', '/miniapp/app/{bot_id:\d+}', ['TokoBot\Controllers\MiniAppController', 'app']);
        $r->addRoute('POST', '/api/miniapp/auth', ['TokoBot\Controllers\MiniAppController', 'authenticate']);
    
        // API routes
        $r->addRoute('GET', '/api/tbot/{id:\d+}/webhook', ['TokoBot\Controllers\BotApiController', 'getWebhookInfo']);
        $r->addRoute('POST', '/api/tbot/{id:\d+}/webhook', ['TokoBot\Controllers\BotApiController', 'setWebhook']);
        $r->addRoute('DELETE', '/api/tbot/{id:\d+}/webhook', ['TokoBot\Controllers\BotApiController', 'deleteWebhook']);});
