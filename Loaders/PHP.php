<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <daniel@bugadani.hu>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\Translation\Loaders;

use Modules\Translation\iLoader;
use OutOfBoundsException;

class PHP implements iLoader
{
    private $strings_dir;

    public function __construct($dir)
    {
        $this->strings_dir = $dir;
    }

    public function load($lang)
    {
        $file = $this->strings_dir . '/' . $lang . '.php';
        if (!file_exists($file)) {
            throw new OutOfBoundsException('Language data not found for language: ' . $lang);
        }
        return include $file;
    }

}