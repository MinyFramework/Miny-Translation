<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <daniel@bugadani.hu>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\Translation;

use Miny\Application\BaseApplication;

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
        $factory    = $app->getFactory();

        $factory->add('translation', __NAMESPACE__ . '\Translation')
                ->setArguments('@translation', '@translation:loaders:{@translation:loader}');

        $this->ifModule('Templating', function() use($factory) {
            $factory->add('translation_function', '\Modules\Templating\Compiler\Functions\CallbackFunction')
                    ->setArguments('t', '*translation::get');
            $factory->getBlueprint('template_environment')
                    ->addMethodCall('addFunction', '&translation_function');
        });
    }
}
