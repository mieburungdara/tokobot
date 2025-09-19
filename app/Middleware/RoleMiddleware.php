<?php

namespace TokoBot\Middleware;

use TokoBot\Helpers\Session;
use TokoBot\Helpers\Logger;
use TokoBot\Services\AuthorizationService;

class RoleMiddleware extends Middleware
{
    protected string $requiredRole;
    protected AuthorizationService $authService;

    public function __construct(string $requiredRole, AuthorizationService $authService)
    {
        $this->requiredRole = $requiredRole;
        $this->authService = $authService;
    }

    public function handle(callable $next)
    {
        if (!$this->authService->check($this->requiredRole)) {
            Logger::channel('auth')->warning('RoleMiddleware: Access denied.', [
                'current_user_role' => Session::get('user_role', 'guest'),
                'required_role' => $this->requiredRole
            ]);
            $errorController = new \TokoBot\Controllers\ErrorController();
            $errorController->forbidden();
            exit();
        }
        return $next();
    }
}
