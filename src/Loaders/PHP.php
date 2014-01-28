<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <daniel@bugadani.hu>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\Translation\Loaders;

use InvalidArgumentException;
use Modules\Translation\iLoader;

class PHP implements iLoader
{
    public static function load($dir, $lang)
    {
        $file = sprintf('%s/%s.php', $dir, $lang);
        if (!file_exists($file)) {
            throw new InvalidArgumentException('Language file not found: ' . $lang);
        }
        return include $file;
    }

}
