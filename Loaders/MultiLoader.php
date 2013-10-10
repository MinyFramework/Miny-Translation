<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <daniel@bugadani.hu>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\Translation\Loaders;

class MultiLoader implements iLoader
{
    private $loaders = array();

    public function addLoader(iLoader $loader)
    {
        $this->loaders[] = $loader;
    }

    public function load($lang)
    {
        $strings = array();
        foreach ($this->loaders as $loader) {
            $strings = $loader->load($lang) + $strings;
        }
    }

}