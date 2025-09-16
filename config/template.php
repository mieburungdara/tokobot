<?php

// config/template.php

return [
    // Meta and Open Graph
    'author' => 'pixelcave',
    'robots' => 'index, follow',
    'title' => 'Dashmix - Bootstrap 5 Admin Template & UI Framework',
    'description' => 'Dashmix - Bootstrap 5 Admin Template & UI Framework created by pixelcave',
    'og_url_site' => '',
    'og_url_image' => '',

    // General
    'theme' => '',
    'page_loader' => false,
    'remember_theme' => true,

    // Layout
    'l_sidebar_left' => true,
    'l_sidebar_mini' => false,
    'l_sidebar_visible_desktop' => true,
    'l_sidebar_visible_mobile' => false,
    'l_sidebar_dark' => true, // Overridden from backend config
    'l_side_overlay_hoverable' => false,
    'l_side_overlay_visible' => false,
    'l_page_overlay' => true,
    'l_side_scroll' => true,
    'l_header_fixed' => true,
    'l_header_style' => 'light', // Overridden from backend config
    'l_footer_fixed' => false,
    'l_m_content' => '', // Overridden from backend config

    // Included Views
    'inc_side_overlay' => VIEWS_PATH . '/inc/backend/views/inc_side_overlay.php',
    'inc_sidebar' => VIEWS_PATH . '/inc/backend/views/inc_sidebar.php',
    'inc_header' => VIEWS_PATH . '/inc/backend/views/inc_header.php',
    'inc_footer' => VIEWS_PATH . '/inc/backend/views/inc_footer.php',

    // Navigation
    'main_nav_active' => basename($_SERVER['PHP_SELF']),
    'main_nav' => require_once(CONFIG_PATH . '/admin_menu.php'),
];
