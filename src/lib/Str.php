<?php
/**
 * Copyright (c) 2024. by zed-simangunsong
 *
 * @license     MIT License
 * @copyright   Copyright (2) 2024, zed-simangunsong
 */

namespace Zed\Test\Lib;


class Str
{
    public static function camelcase($string, $delimiter = [' ', '-', '_'])
    {
        $string = str_replace($delimiter, ' ', $string);

        return lcfirst(self::camelize($string));
    }

    /**
     * Convert string into camelize pattern.
     *
     * @param $string
     * @param string $delimiter
     * @param bool $camelCase
     * @return mixed
     */
    public static function camelize($string, $delimiter = ' ', $camelCase = true)
    {
        if (is_bool($delimiter)) {
            $camelCase = $delimiter;
            $delimiter = ' ';
        }

        $string = array_map('ucfirst', explode($delimiter, strtolower($string)));

        return implode($camelCase ? '' : $delimiter, $string);
    }
}