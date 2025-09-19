<?php

// bootstrap/app.php

define('ROOT_PATH', dirname(__DIR__));
define('VIEWS_PATH', ROOT_PATH . '/views');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');

require_once ROOT_PATH . '/vendor/autoload.php';

use TokoBot\Core\Container;
use TokoBot\Helpers\Logger;
use TokoBot\Helpers\Session;

// --- Application Service Providers ---
$providers = [
    TokoBot\Core\ServiceProviders\RoutingServiceProvider::class,
    TokoBot\Core\ServiceProviders\CacheServiceProvider::class,
    TokoBot\Core\ServiceProviders\TemplateServiceProvider::class,
];

// --- DI Container Setup ---
$container = new Container();

foreach ($providers as $providerClass) {
    /** @var \TokoBot\Core\ServiceProviders\ServiceProviderInterface $provider */
    $provider = new $providerClass();
    $provider->register($container);
}

// --- Global Error & Exception Handling ---
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

// --- Session & Environment ---
Session::start();

$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

return $container;