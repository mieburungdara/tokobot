<?php

namespace TokoBot\Core\ServiceProviders;

use TokoBot\Core\Container;

/**
 * Defines the contract for a service provider.
 */
interface ServiceProviderInterface
{
    /**
     * Register the service provider's services with the container.
     *
     * @param Container $container
     * @return void
     */
    public function register(Container $container): void;
}
