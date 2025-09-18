<?php

namespace TokoBot\Middleware;

use TokoBot\Helpers\Session;
use TokoBot\Helpers\Logger;

class AuthSourceMiddleware extends Middleware
{
    protected array $requiredSources;

    public function __construct(array $requiredSources)
    {
        $this->requiredSources = $requiredSources;
    }

    public function handle(callable $next)
    {
        $currentAuthSource = Session::get('auth_source');
        Logger::channel('auth')->info('AuthSourceMiddleware: Checking access.', [
            'current_auth_source' => $currentAuthSource,
            'required_sources' => $this->requiredSources
        ]);

        if (!in_array($currentAuthSource, $this->requiredSources)) {
            Logger::channel('auth')->warning('AuthSourceMiddleware: Access denied.', [
                'current_auth_source' => $currentAuthSource,
                'required_sources' => $this->requiredSources
            ]);
            $errorController = new \TokoBot\Controllers\ErrorController();
            $errorController->forbidden();
            exit();
        }
        return $next();
    }
}
