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
        return isset(self::$lang_rules[$lang]) ? self::$lang_rules[$lang] : array();
    }

    public function __construct($lang, Loader $loader)
    {
        foreach (self::getRules($lang) as $name => $rule) {
            $rule = preg_replace('/[^n0-9\w=\-+%<>]/', '', $rule);
            $this->rules[$name] = $rule;
        }
        $this->strings = $loader->load($lang);
    }

    public function addStrings(array $strings)
    {
        foreach ($strings as $key => $string) {
            $this->addString($key, $string);
        }
    }

    public function addString($key, $string)
    {
        if (is_array($string) && count($string) == 1) {
            $string = current($string);
        }
        $this->strings[$key] = $string;
    }

    private function getStringForN(array $string, $num)
    {
        $fallback = NULL;
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

    public function get($key, $num = NULL)
    {
        if (isset($this->strings[$key])) {
            $string = $this->strings[$key];
        } else {
            $string = $key;
        }
        if (is_array($string)) {
            $string = $this->getStringForN($string, $num) ? : $key;
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