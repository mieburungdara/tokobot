<?php

namespace TokoBot\Controllers;

class PageController extends DashmixController
{
    public function support()
    {
        $breadcrumbs = [['name' => 'Support']];
        $this->renderDashmix(
            VIEWS_PATH . '/pages/support.php',
            'Support',
            'Get help and support.',
            [],
            $breadcrumbs
        );
    }

    public function contact()
    {
        $breadcrumbs = [['name' => 'Contact']];
        $this->renderDashmix(
            VIEWS_PATH . '/pages/contact.php',
            'Contact Us',
            'Get in touch with us.',
            [],
            $breadcrumbs
        );
    }
}
