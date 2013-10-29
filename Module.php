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
        $app->add('translation', __NAMESPACE__ . '\Translation');
        $app->getBlueprint('view_helpers')
                ->addMethodCall('addMethod', 't', '*translation::get');
    }

}