<?php
/**
 * Copyright (c) 2024. by zed-simangunsong
 *
 * @license     MIT License
 * @copyright   Copyright (2) 2024, zed-simangunsong
 */

namespace Zed\Test\App\Models;


class ZoneModel
{
    protected static $codes = [
        'JHR01',
        'JHR02',
        'JHR03',
        'JHR04',
        'KDH01',
        'KDH02',
        'KDH03',
        'KDH04',
        'KDH05',
        'KDH06',
        'KDH07',
        'KTN01',
        'KTN03',
        'MLK01',
        'NGS01',
        'NGS02',
    ];

    /**
     * @return array
     */
    public static function getCodes()
    {
        return self::$codes;
    }

    public static function getBoxByZone($zone)
    {
        return (new BoxModel)->getBoxByZone($zone);
    }
}