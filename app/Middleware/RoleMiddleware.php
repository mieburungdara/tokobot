<?php

namespace TokoBot\Middleware;

use TokoBot\Helpers\Auth;
use TokoBot\Helpers\Session;
use TokoBot\Helpers\Logger;

class RoleMiddleware extends Middleware
{
    protected string $requiredRole;

    public function __construct(string $requiredRole)
    {
        $this->requiredRole = $requiredRole;
    }

    public function handle(callable $next)
    {
        $currentUserRole = Session::get('user_role');
        Logger::channel('auth')->info('RoleMiddleware: Checking access.', [
            'current_user_role' => $currentUserRole,
            'required_role' => $this->requiredRole
        ]);

        if (!Auth::check() || !Auth::hasRole($this->requiredRole)) {
            Logger::channel('auth')->warning('RoleMiddleware: Access denied.', [
                'current_user_role' => $currentUserRole,
                'required_role' => $this->requiredRole,
                'is_authenticated' => Auth::check()
            ]);
            $errorController = new \TokoBot\Controllers\ErrorController();
            $errorController->forbidden();
            exit();
        }
        return $next();
    }
}
