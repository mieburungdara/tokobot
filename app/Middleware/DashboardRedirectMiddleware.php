<?php

namespace TokoBot\Middleware;

use TokoBot\Helpers\Session;

class DashboardRedirectMiddleware extends Middleware
{
    /**
     * Handle the incoming request by redirecting the user to the appropriate dashboard based on their role.
     *
     * @param callable $next The next middleware in the pipeline.
     * @return never This method always terminates by redirecting.
     */
    public function handle(callable $next)
    {
        $userRole = Session::get('user_role', 'guest');

        $url = match ($userRole) {
            'admin' => '/admin/dashboard',
            'member' => '/member/dashboard',
            default => '/xoradmin', // Redirect guests to login
        };

        header('Location: ' . $url);
        exit();
    }
}
