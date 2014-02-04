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
use Modules\Templating\Environment;

class Module extends \Miny\Modules\Module
{

    public function defaultConfiguration()
    {
        return array(
            'translation' => array(
                'strings' => array(),
                'loaders' => array(
                    'php' => __NAMESPACE__ . '\Loaders\PHP'
                )
            )
        );
    }

    public function init(BaseApplication $app)
    {
        $container = $app->getContainer();

        $container->addAlias(
            __NAMESPACE__ . '\Translation',
            null,
            array('@translation', '@translation:loaders:{@translation:loader}')
        );

        $this->ifModule(
            'Templating',
            function () use ($container) {
                $container->addCallback(
                    '\\Modules\\Templating\\Environment',
                    function (Environment $environment, Container $container) {
                        $environment->addFunction(
                            new \Modules\Templating\Compiler\Functions\CallbackFunction('t', array(
                                $container->get(__NAMESPACE__.'\\Translation', 'get')
                            ))
                        );
                    }
                );

            }
        );
    }
}
