<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <daniel@bugadani.hu>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\Translation;

class Translation
{
    private $strings = array();
    private $rules = array();
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

    public function __construct($lang, iLoader $loader)
    {
        $this->rules = self::getRules($lang);
        $this->addStrings($loader->load($lang));
    }

    public function addStrings(array $strings)
    {
        foreach ($strings as &$string) {
            if (is_array($string) && count($string) == 1) {
                $string = current($string);
            }
        }
        $this->strings = $strings + $this->strings;
    }

    public function addString($key, $string)
    {
        if (is_array($string) && count($string) == 1) {
            $string = current($string);
        }
        $this->strings[$key] = $string;
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

    public function get($string, $num = NULL)
    {
        if (isset($this->strings[$string])) {
            $string = $this->strings[$string];
        }

        if (is_array($string)) {
            $string = $this->getPluralString($string, $num);
        }

        $arg_num = func_num_args();
        if ($arg_num > 1) {
            $keys = array();
            $vals = array();
            for ($i = 1; $i < $arg_num; ++$i) {
                $keys[] = '{' . ($i - 1) . '}';
                $vals[] = func_get_arg($i);
            }
            $string = str_replace($keys, $vals, $string);
        }
        return $string;
    }

}