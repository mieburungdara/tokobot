<?php

namespace TokoBot\Middleware;

use TokoBot\Helpers\Session;

class AuthSourceMiddleware extends Middleware
{
    protected array $requiredSources;

    public function __construct(array $requiredSources)
    {
        $this->requiredSources = $requiredSources;
    }

    public function handle(callable $next)
    {
        if (!in_array(Session::get('auth_source'), $this->requiredSources)) {
            http_response_code(403);
            require_once VIEWS_PATH . '/errors/403.php';
            exit();
        }
        return $next();
    }
}
