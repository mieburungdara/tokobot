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

    public static function flash($key, $value = null)
    {
        self::start();

        $sessionKey = 'flash_' . $key;

        if ($value !== null) {
            self::set($sessionKey, $value);
        } else {
            $flashValue = self::get($sessionKey);
            self::remove($sessionKey);
            return $flashValue;
        }
        return null; // Only returns a value when getting
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

    /**
     * Generate a CSRF token, store it in the session, and return it.
     *
     * @return string
     * @throws \Exception
     */
    public static function generateCsrfToken(): string
    {
        self::start();
        $token = bin2hex(random_bytes(32));
        self::set('csrf_token', $token);
        return $token;
    }

    /**
     * Validate a CSRF token against the one stored in the session.
     *
     * @param string|null $token The token from the request.
     * @return bool
     */
    public static function validateCsrfToken(?string $token): bool
    {
        self::start();
        $sessionToken = self::get('csrf_token');
        if (!$token || !$sessionToken) {
            return false;
        }

        // Use hash_equals for timing-attack-safe comparison
        $isValid = hash_equals($sessionToken, $token);
        self::remove('csrf_token'); // One-time use token
        return $isValid;
    }
}
