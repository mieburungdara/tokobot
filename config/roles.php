<?php

// config/roles.php
// Mendefinisikan peran mana yang dapat mengakses metode controller tertentu.

return [
    // Format: 'Namespace\\Controller@method' => [array_peran_yang_diizinkan]

    // Dashboard bisa diakses admin dan member
    'TokoBot\\Controllers\\DashboardController@index' => ['admin', 'member'],

    // Metode khusus AdminController
    'TokoBot\\Controllers\\AdminController@users' => ['admin'],
    'TokoBot\\Controllers\\AdminController@reports' => ['admin', 'member'],
    'TokoBot\\Controllers\\AdminController@settings' => ['admin', 'member'],
    'TokoBot\Controllers\AdminController@manageBots' => ['admin'],
    'TokoBot\Controllers\AdminController@addBot' => ['admin'],
    'TokoBot\Controllers\AdminController@deleteBot' => ['admin'],

    // API routes
    'TokoBot\Controllers\BotApiController@getWebhookInfo' => ['admin'],
    'TokoBot\Controllers\BotApiController@setWebhook' => ['admin'],
    'TokoBot\Controllers\BotApiController@deleteWebhook' => ['admin'],

    // Metode khusus MemberController
    'TokoBot\\Controllers\\MemberController@index' => ['member'],

    // Rute HomeController bersifat publik, jadi tidak perlu dimasukkan ke sini.
    // Jika sebuah rute tidak ada di peta ini, kita anggap publik.

    // Auth routes
    'TokoBot\Controllers\AuthController@showLoginForm' => ['guest'],
    'TokoBot\Controllers\AuthController@handleLogin' => ['guest'],
    'TokoBot\Controllers\AuthController@handleTokenLogin' => ['guest'],
    'TokoBot\Controllers\AuthController@logout' => ['admin', 'member'],
];
