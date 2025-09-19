<?php

// public/index.php

// Set error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED);

// Bootstrap the application
$container = require_once __DIR__ . '/../bootstrap/app.php';

// Create the application instance
$app = new \TokoBot\Core\App($container);

// Make the container globally accessible for helpers, etc.
\TokoBot\Core\App::setContainer($container);

$app->run();
