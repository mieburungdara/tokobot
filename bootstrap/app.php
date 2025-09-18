<?php

// bootstrap/app.php

// Define global path constants
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');

require_once ROOT_PATH . '/vendor/autoload.php';

use TokoBot\Core\Container;
use TokoBot\Helpers\Logger;
use TokoBot\Helpers\Session;

// --- Start DI Container Setup ---

// Require the Template class since it's not namespaced and autoloaded
require_once VIEWS_PATH . '/inc/_classes/Template.php';

$container = new Container();

// Create the template object
$dm = new Template('Dashmix', '5.10', '/assets');

// Load template configuration
$templateConfig = require_once CONFIG_PATH . '/template.php';

// Apply configuration to the template object
foreach ($templateConfig as $key => $value) {
    $dm->$key = $value;
}

// Store the configured template object in the container
$container->set('template', $dm);

// Register Cache Service (PSR-16)
use Psr\SimpleCache\CacheInterface;
use TokoBot\Helpers\ApcuCache;

$container->set(CacheInterface::class, function () {
    try {
        return new ApcuCache();
    } catch (\Exception $e) {
        // Log the error if APCu is not available
        Logger::channel('critical')->warning('APCu cache initialization failed: ' . $e->getMessage());
        // Fallback to a dummy/null cache object if needed, or re-throw.
        // For now, we let it fail during bootstrap if APCu is expected.
        throw $e;
    }
});

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

return $container;
