<?php

// config/roles.php
/**
 * Defines the roles and permissions in the application.
 * The key is the role name.
 * - display_name: The human-readable name for the role.
 * - inherits: An array of other roles from which this role inherits permissions.
 * - permissions: An array of specific permissions assigned to this role.
 */
return [
    'admin' => [
        'display_name' => 'Administrator',
        'inherits' => ['member'],
        'permissions' => [
            'manage_users',    // Ability to view and manage users
            'manage_roles',    // Ability to change user roles
            'manage_settings', // Ability to change application settings
            'view_reports',    // Ability to view system reports
            'view_analytics',  // Ability to view bot analytics
            'manage_bots',     // Ability to add/remove bots
            'manage_channels', // Ability to manage storage channels
            'view_logs',       // Ability to view system logs
            'manage_cache',    // Ability to clear cache
        ],
    ],
    'member' => [
        'display_name' => 'Member',
        'inherits' => [],
        'permissions' => [
            'view_dashboard', // Basic permission to see their own dashboard
        ],
    ],
    'guest' => [
        'display_name' => 'Guest',
        'inherits' => [],
        'permissions' => [],
    ],
];