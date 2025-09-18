<?php

namespace TokoBot\Middleware;

use TokoBot\Helpers\Auth;

class RoleMiddleware extends Middleware
{
    protected string $requiredRole;

    public function __construct(string $requiredRole)
    {
        $this->requiredRole = $requiredRole;
    }

    public function handle(callable $next)
    {
        if (!Auth::check() || !Auth::hasRole($this->requiredRole)) {
            // Redirect to an unauthorized page or show an error
            http_response_code(403);
            echo "Unauthorized: You do not have the required role.";
            exit();
        }
        return $next();
    }
}
