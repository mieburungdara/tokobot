<?php
/**
 * backend/config.php
 *
 * Author: pixelcave
 *
 * Backend pages configuration file
 *
 */

// **************************************************************************************************
// INCLUDED VIEWS
// **************************************************************************************************

$dm->inc_side_overlay           = __DIR__ . '/views/inc_side_overlay.php';
$dm->inc_sidebar                = __DIR__ . '/views/inc_sidebar.php';
$dm->inc_header                 = __DIR__ . '/views/inc_header.php';
$dm->inc_footer                 = __DIR__ . '/views/inc_footer.php';


// **************************************************************************************************
// SIDEBAR
// **************************************************************************************************

$dm->l_sidebar_dark             = true;


// **************************************************************************************************
// HEADER
// **************************************************************************************************

$dm->l_header_style             = 'light';


// **************************************************************************************************
// MAIN CONTENT
// **************************************************************************************************

$dm->l_m_content                = '';


// **************************************************************************************************
// MAIN MENU
// **************************************************************************************************

$dm->main_nav                   = array(
    array(
        'name'  => 'Admin Dashboard',
        'icon'  => 'si si-speedometer',
        'url'   => '/dashboard',
        'roles' => ['admin']
    ),
    array(
        'name'  => 'Management',
        'icon'  => 'si si-puzzle',
        'roles' => ['admin', 'member'], // Diperbarui
        'sub'   => array(
            array(
                'name'  => 'Users',
                'url'   => '/users',
                'roles' => ['admin'] // Dibuat spesifik untuk admin
            ),
            array(
                'name'  => 'Reports',
                'url'   => '/reports' // Tanpa 'roles', jadi ikut parent
            )
        )
    ),
    array(
        'name'  => 'Settings',
        'icon'  => 'si si-settings',
        'url'   => '/settings',
        'roles' => ['admin', 'member'] // Diperbarui
    ),
    array(
        'name'  => 'Member Dashboard',
        'icon'  => 'si si-user',
        'url'   => '/dashboard',
        'roles' => ['member']
    )
);
