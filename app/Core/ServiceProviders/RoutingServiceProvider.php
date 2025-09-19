<?php

namespace TokoBot\Core\ServiceProviders;

use TokoBot\Core\Container;
use TokoBot\Core\Routing\RouteLoader;

class RoutingServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->set('dispatcher', function () {
            define('APP_PATH', ROOT_PATH . '/app');
            $loader = new RouteLoader([APP_PATH . '/Controllers']);
            return $loader->register();
        });
    }
}
