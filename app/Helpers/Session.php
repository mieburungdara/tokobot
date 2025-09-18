<?php

namespace TokoBot\Helpers;

class Session
{
    public static function start()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public static function remove($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function flash($key, $message = null)
    {
        self::start(); // Ensure session is started

        if ($message) {
            self::set('flash_' . $key, $message);
        } else {
            $flashMessage = self::get('flash_' . $key);
            self::remove('flash_' . $key);
            return $flashMessage;
        }
    }

    public static function clear()
    {
        session_unset();
        session_destroy();
    }

    /**
     * Checks if the logged-in user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public static function hasRole(string $role): bool
    {
        $currentUserRole = self::get('user_role');
        Logger::channel('auth')->info('Session::hasRole: Comparing roles.', [
            'current_user_role_in_session' => $currentUserRole,
            'role_being_checked' => $role,
            'comparison_result' => ($currentUserRole === $role)
        ]);
        return $currentUserRole === $role;
    }
}
