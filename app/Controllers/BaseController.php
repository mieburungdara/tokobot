<?php

namespace TokoBot\Controllers;

use TokoBot\Core\Container;

class BaseController
{
    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    protected function render($contentView, $pageTitle)
    {
        require_once $contentView;
    }
}
