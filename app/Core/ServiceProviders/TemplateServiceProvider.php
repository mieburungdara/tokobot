<?php

namespace TokoBot\Core\ServiceProviders;

use TokoBot\Core\Container;
use Template;

class TemplateServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->set('template', function () {
            // Require the Template class since it's not namespaced and autoloaded
            require_once VIEWS_PATH . '/inc/_classes/Template.php';

            $dm = new Template('Dashmix', '5.10', '/assets');

            // Load template configuration
            $templateConfig = require_once CONFIG_PATH . '/template.php';

            // Apply configuration to the template object
            foreach ($templateConfig as $key => $value) {
                $dm->$key = $value;
            }

            return $dm;
        });
    }
}
