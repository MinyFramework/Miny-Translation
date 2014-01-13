<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <daniel@bugadani.hu>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\Translation;

use Miny\Application\BaseApplication;

class Module extends \Miny\Application\Module
{

    public function init(BaseApplication $app)
    {
        $parameters                        = $app->getParameters();
        $parameters['translation:strings'] = array();
        $parameters['translation:loaders'] = array(
            'php' => __NAMESPACE__ . '\Loaders\PHP'
        );

        $app->add('translation', __NAMESPACE__ . '\Translation')
                ->setArguments('@translation', $parameters, '@translation:loaders:{@translation:loader}');


        $this->ifModule('Templating', function()use($app) {
            $app->add('translation_function', '\Modules\Templating\Compiler\Functions\CallbackFunction')
                    ->setArguments('t', '*translation::get');
            $app->getBlueprint('template_environment')
                    ->addMethodCall('addFunction', '&translation_function');
        });

    }
}
