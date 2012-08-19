<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <daniel@bugadani.hu>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\Translation;

use Miny\Application\Application;

class Module extends \Miny\Application\Module
{
    public function init(Application $app)
    {
        $app->add('translation', __NAMESPACE__ . '\Translation');
        $app->getBlueprint('view')
                ->addMethodCall('addMethod', 't', '*translation::get');
    }

}