<?php

namespace TokoBot\Middleware;

use TokoBot\Helpers\Auth;

class AuthMiddleware extends Middleware
{
    public function handle(callable $next)
    {
        if (!Auth::check()) {
            // Redirect to login page or show an error
            header('Location: /xoradmin');
            exit();
        }
        return $next();
    }
}
