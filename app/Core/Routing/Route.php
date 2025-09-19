<?php

namespace TokoBot\Core\Routing;

use Attribute;

/**
 * A PHP 8 attribute to define a route on a controller method.
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Route
{
    /**
     * @param string $path The URL path for the route (e.g., '/users/{id}').
     * @param string $method The HTTP method (GET, POST, etc.).
     * @param array $middleware An array of middleware classes to apply to this route.
     * @param string|null $name An optional name for the route.
     */
    public function __construct(
        public string $path,
        public string $method = 'GET',
        public array $middleware = [],
        public ?string $name = null
    ) {}
}
