<?php

namespace TokoBot\Middleware;

abstract class Middleware
{
    /**
     * Handle the incoming request.
     *
     * @param callable $next The next middleware in the pipeline or the final request handler.
     * @return mixed
     */
    abstract public function handle(callable $next);
}
