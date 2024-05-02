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
    public static function camelCase($string, $delimiter = [' ', '-', '_'])
    {
        $string = str_replace($delimiter, ' ', $string);

        return lcfirst(self::pascalCase($string));
    }

    /**
     * Convert string into camelize pattern.
     *
     * @param $string
     * @param string $delimiter
     * @param bool $keepDelimiter
     * @return mixed
     */
    public static function pascalCase($string, $delimiter = ' ', $keepDelimiter = true)
    {
        if (is_bool($delimiter)) {
            $keepDelimiter = $delimiter;
            $delimiter = ' ';
        }

        $string = array_map('ucfirst', explode($delimiter, strtolower($string)));

        return implode($keepDelimiter ? '' : $delimiter, $string);
    }
}