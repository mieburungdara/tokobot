<?php

namespace TokoBot\Core\ServiceProviders;

use TokoBot\Core\Container;
use TokoBot\Helpers\Logger;
use Psr\SimpleCache\CacheInterface;
use TokoBot\Helpers\ApcuCache;

class CacheServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->set(CacheInterface::class, function () {
            try {
                return new ApcuCache();
            } catch (\Exception $e) {
                Logger::channel('critical')->warning('APCu cache initialization failed: ' . $e->getMessage());
                // In a real app, you might want to fallback to a NullCache or FileCache object.
                // For now, we re-throw the exception to make it clear that caching is not available.
                throw $e;
            }
        });
    }
}
