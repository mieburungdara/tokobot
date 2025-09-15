<?php

namespace TokoBot\Controllers;

class BaseController
{
    protected function render($contentView, $pageTitle)
    {
        require_once $contentView;
    }
}
