<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <daniel@bugadani.hu>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\Translation;

use InvalidArgumentException;
use Miny\Factory\ParameterContainer;

class Translation
{
    private static $lang_rules = array(
        'hu' => array(),
        'en' => array(),
    );

    public static function addRules($lang, array $rules)
    {
        self::$lang_rules[$lang] = $rules;
    }

    public static function getRules($lang)
    {
        $return = array();
        if (isset(self::$lang_rules[$lang])) {
            foreach (self::$lang_rules[$lang] as $name => $rule) {
                $return[$name] = preg_replace('/[^n0-9\w=\-+%<>]/', '', $rule);
            }
        }
        return $return;
    }

    /**
     * @var string[]
     */
    private $rules = array();

    /**
     * @var ParameterContainer
     */
    private $container;

    public function __construct(array $config, ParameterContainer $container, $loader_class)
    {
        $lang = $config['language'];
        $strings = $loader_class::load($config['directory'], $config['language']);

        $this->rules = self::getRules($lang);
        $this->container = $container;
        $this->addStrings($strings);
    }

    private function normalize($string)
    {
        if (is_array($string) && count($string) == 1) {
            $string = current($string);
        }
        return $string;
    }

    public function addStrings(array $strings)
    {
        foreach ($strings as &$string) {
            $string = $this->normalize($string);
        }
        $this->container['translation:strings'] += $strings;
    }

    public function addString($key, $string)
    {
        $string = $this->normalize($string);
        $this->container['translation:strings'] += array($key => $string);
    }

    private function getPluralString(array $string, $num)
    {
        $fallback = $string;
        foreach ($string as $q => $str) {
            if (is_int($q)) {
                if ($num === $q) {
                    return $str;
                }
            } elseif ($q == 'other') {
                $fallback = $str;
            } elseif ($this->ruleApplies($q, $num)) {
                return $str;
            }
        }
        return $fallback;
    }

    private function ruleApplies($rule, $num)
    {
        if (!isset($this->rules[$rule]) || !is_int($num)) {
            return false;
        }
        $rule = str_replace('n', $num, $this->rules[$rule]);
        return eval('return (' . $rule . ');');
    }

    private function replacePlaceholders($string, array $parameters)
    {
        if (!empty($parameters)) {
            $replaces = array();
            foreach ($parameters as $i => $arg) {
                $replaces['{' . $i . '}'] = $arg;
            }
            $string = str_replace(array_keys($replaces), $replaces, $string);
        }
        return $string;
    }

    private function getTranslated($string)
    {
        $strings = $this->container['translation']['strings'];
        if (isset($strings[$string])) {
            $string = $strings[$string];
        }
        return $string;
    }

    public function get($string)
    {
        if (is_array($string)) {
            $args = $string;
        } else {
            $args = func_get_args();
        }
        $untranslated = array_shift($args);
        $string = $this->getTranslated($untranslated);

        if (is_array($string)) {
            if (!isset($args[0])) {
                throw new InvalidArgumentException('Must supply a quantity for plural strings.');
            }
            $string = $this->getPluralString($string, $args[0]);
        }
        return $this->replacePlaceholders($string, $args);
    }

}
