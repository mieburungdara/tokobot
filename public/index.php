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

// Create and configure the template object
$dm = new Template('Dashmix', '5.10', 'assets');

// --- Start Configuration from _global/config.php ---
$dm->author                     = 'pixelcave';
$dm->robots                     = 'index, follow';
$dm->title                      = 'Dashmix - Bootstrap 5 Admin Template &amp; UI Framework';
$dm->description                = 'Dashmix - Bootstrap 5 Admin Template &amp; UI Framework created by pixelcave';
$dm->og_url_site                = '';
$dm->og_url_image               = '';
$dm->theme                      = '';
$dm->page_loader                = false;
$dm->remember_theme             = true;
$dm->inc_side_overlay           = '';
$dm->inc_sidebar                = '';
$dm->inc_header                 = '';
$dm->inc_footer                 = '';
$dm->l_sidebar_left             = true;
$dm->l_sidebar_mini             = false;
$dm->l_sidebar_visible_desktop  = true;
$dm->l_sidebar_visible_mobile   = false;
$dm->l_sidebar_dark             = false;
$dm->l_side_overlay_hoverable   = false;
$dm->l_side_overlay_visible     = false;
$dm->l_page_overlay             = true;
$dm->l_side_scroll              = true;
$dm->l_header_fixed             = true;
$dm->l_header_style             = 'dark';
$dm->l_footer_fixed             = false;
$dm->l_m_content                = '';
$dm->main_nav_active            = basename($_SERVER['PHP_SELF']);
$dm->main_nav                   = array();
// --- End Configuration from _global/config.php ---

// --- Start Configuration from backend/config.php ---
$dm->inc_side_overlay           = VIEWS_PATH . '/inc/backend/views/inc_side_overlay.php';
$dm->inc_sidebar                = VIEWS_PATH . '/inc/backend/views/inc_sidebar.php';
$dm->inc_header                 = VIEWS_PATH . '/inc/backend/views/inc_header.php';
$dm->inc_footer                 = VIEWS_PATH . '/inc/backend/views/inc_footer.php';
$dm->l_sidebar_dark             = true;
$dm->l_header_style             = 'light';
$dm->l_m_content                = '';
$dm->main_nav                   = require_once(CONFIG_PATH . '/admin_menu.php');

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
        // Create a new container and template for the error page
        $errorContainer = new \TokoBot\Core\Container();
        $dm_error = new \Template('Dashmix', '5.10', 'assets');
        $errorContainer->set('template', $dm_error);

        $errorController = new \TokoBot\Controllers\ErrorController($errorContainer);
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
        $errorController = new \TokoBot\Controllers\ErrorController($container);
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
                $errorController = new \TokoBot\Controllers\ErrorController($container);
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