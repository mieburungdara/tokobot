<?php

namespace TokoBot\Core\ServiceProviders;

use TokoBot\Core\Container;
use TokoBot\Services\AuthorizationService;

class AuthorizationServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        // Register as a singleton
        $container->set(AuthorizationService::class, function () {
            return new AuthorizationService();
        });
    }
}
