<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <daniel@bugadani.hu>
 *
 * For licensing information see the LICENCE file.
 */

namespace Modules\Translation;

use Modules\Templating\Compiler\TemplateFunction;
use Modules\Templating\Extension;

class TemplateExtension extends Extension
{
    private $translation;

    public function __construct(Translation $translation)
    {
        $this->translation = $translation;
    }

    public function getExtensionName()
    {
        return 'translation';
    }

    public function getFunctions()
    {
        $functions = array(
            new TemplateFunction('t', array($this->translation, 'get')),
        );

        return $functions;
    }

}
