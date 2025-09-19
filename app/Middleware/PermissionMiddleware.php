<?php

namespace TokoBot\Middleware;

use TokoBot\Helpers\Session;
use TokoBot\Helpers\Logger;
use TokoBot\Services\AuthorizationService;

class PermissionMiddleware extends Middleware
{
    protected string $requiredPermission;
    protected AuthorizationService $authService;

    public function __construct(string $requiredPermission, AuthorizationService $authService)
    {
        $this->requiredPermission = $requiredPermission;
        $this->authService = $authService;
    }

    public function handle(callable $next)
    {
        if (!$this->authService->can($this->requiredPermission)) {
            Logger::channel('auth')->warning('PermissionMiddleware: Access denied.', [
                'user_role' => Session::get('user_role', 'guest'),
                'required_permission' => $this->requiredPermission
            ]);
            // In a real app, you might want a specific "403 Forbidden" view.
            $errorController = new \TokoBot\Controllers\ErrorController();
            $errorController->forbidden();
            exit();
        }
        return $next();
    }
}
