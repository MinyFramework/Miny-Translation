<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <daniel@bugadani.hu>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\Translation;

use Miny\Application\BaseApplication;
use Miny\Factory\Container;
use Minty\Environment;

class Module extends \Miny\Modules\Module
{

    public function defaultConfiguration()
    {
        return array(
            'strings' => array()
        );
    }

    public function init(BaseApplication $app)
    {
        $container = $app->getContainer();
        $container->addConstructorArguments(
            __NAMESPACE__ . '\\Translation',
            $this->getConfigurationTree()
        );

        $this->ifModule(
            'Templating',
            function () use ($container) {
                $container->addCallback(
                    '\\Minty\\Environment',
                    function (Environment $environment, Container $container) {
                        $environment->addExtension(
                            new TemplateExtension(
                                $container->get(__NAMESPACE__ . '\\Translation')
                            )
                        );
                    }
                );
            }
        );
    }
}
