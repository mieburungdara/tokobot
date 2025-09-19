<?php

namespace TokoBot\Helpers;

class Auth
{
    /**
     * Checks if a user is logged in.
     *
     * @return bool
     */
    public static function check(): bool
    {
        return Session::has('user_id');
    }

    /**
     * Returns the logged-in user's data.
     *
     * @return array|null
     */
    public static function user(): ?array
    {
        return Session::get('user');
    }

    /**
     * Returns the logged-in user's ID.
     *
     * @return int|null
     */
    public static function id(): ?int
    {
        return Session::get('user_id');
    }

    /**
     * Checks if the logged-in user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public static function hasRole(string $role): bool
    {
        $sessionRole = Session::get('user_role');
        Logger::channel('auth')->info('Auth::hasRole: Checking role.', [
            'session_role' => $sessionRole,
            'required_role' => $role
        ]);
        return Session::hasRole($role);
    }

    /**
     * Checks if the logged-in user has a specific permission.
     *
     * @param string $permission
     * @return bool
     */
    public static function can(string $permission): bool
    {
        try {
            $authService = \TokoBot\Core\App::getContainer()->get(\TokoBot\Services\AuthorizationService::class);
            return $authService->can($permission);
        } catch (\Exception $e) {
            // If the service is not available, deny permission and log the error.
            Logger::channel('critical')->error('Could not resolve AuthorizationService in Auth::can', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Logs out the current user.
     *
     * @return void
     */
    public static function logout(): void
    {
        Session::clear();
    }
}