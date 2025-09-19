<?php

namespace TokoBot\Services;

use TokoBot\Helpers\Session;

class AuthorizationService
{
    private array $roles;

    public function __construct()
    {
        $this->roles = require CONFIG_PATH . '/roles.php';
    }

    /**
     * Check if the current user has the required role.
     *
     * @param string $requiredRole
     * @return bool
     */
    public function check(string $requiredRole): bool
    {
        $userRole = Session::get('user_role', 'guest');

        // Simple check: does the user have the exact role?
        if ($userRole === $requiredRole) {
            return true;
        }

        // Advanced check: does the user's role inherit the required role?
        if (isset($this->roles[$userRole]) && in_array($requiredRole, $this->roles[$userRole]['inherits'] ?? [])) {
            return true;
        }

        return false;
    }

    /**
     * Check if the current user has ANY of the given roles.
     *
     * @param array $roles
     * @return bool
     */
    public function any(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->check($role)) {
                return true;
            }
        }
        return false;
    }
}
