<?php

namespace TokoBot\Controllers;

use TokoBot\Core\Routing\Route;

class PageController extends DashmixController
{
    #[Route('/support')]
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

    #[Route('/contact')]
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
