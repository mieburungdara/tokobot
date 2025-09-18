<?php

// config/admin_menu.php

return [
    
    [
        'name'  => 'Admin Dashboard',
        'icon'  => 'si si-speedometer',
        'url'   => '/dashboard',
        'roles' => ['admin']
    ],
    [
        'name'  => 'Member Dashboard',
        'icon'  => 'si si-user',
        'url'   => '/dashboard',
        'roles' => ['member']
    ],
    [
        'name'  => 'Management',
        'icon'  => 'si si-puzzle',
        'roles' => ['admin', 'member'], // Diperbarui
        'sub'   => [
            [
                'name'  => 'Users',
                'url'   => '/users',
                'roles' => ['admin'] // Dibuat spesifik untuk admin
            ],
            [
                'name'  => 'Reports',
                'url'   => '/reports' // Tanpa 'roles', jadi ikut parent
            ],
            [
                'name'  => 'Bot Management',
                'url'   => '/bot-management',
                'roles' => ['admin']
            ],
            [
                'name'  => 'Log Viewer',
                'url'   => '/logs',
                'roles' => ['admin']
            ],
            [
                'name'  => 'Storage Channels',
                'url'   => '/storage-channels',
                'roles' => ['admin']
            ],
            [
                'name'  => 'Database Migrations',
                'url'   => '/migrations',
                'roles' => ['admin']
            ]
        ]
    ],
    [
        'name'  => 'Settings',
        'icon'  => 'si si-settings',
        'url'   => '/settings',
        'roles' => ['admin', 'member'] // Diperbarui
    ]
];