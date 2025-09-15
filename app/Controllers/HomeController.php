<?php

namespace TokoBot\Controllers;

class HomeController extends DashmixController
{
    public function index()
    {
        $homeNav = [
            [
                'name' => 'Home',
                'icon' => 'fa fa-home',
                'url' => '/',
                'active' => true
            ],
            [
                'name' => 'Admin Panel',
                'icon' => 'fa fa-user-shield',
                'url' => '/admin/dashboard'
            ],
            [
                'name' => 'Member Panel',
                'icon' => 'fa fa-user',
                'url' => '/dashboard'
            ]
        ];

        $breadcrumbs = [
            ['name' => 'Home']
        ];

        $this->renderDashmix(
            __DIR__ . '/../../views/home.php', // We need to create this view
            'Welcome Home',
            'This is the home page.',
            $homeNav,
            $breadcrumbs
        );
    }
}
