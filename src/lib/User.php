<?php
/**
 * Copyright (c) 2024. by zed-simangunsong
 *
 * @license     MIT License
 * @copyright   Copyright (2) 2024, zed-simangunsong
 */

namespace Zed\Test\Lib;


class User
{
    protected static $instance;

    protected $profile;

    public function __construct($json)
    {
        if (empty($json)) {
            // Default empty profile.
            $json = json_encode([
                'id' => null,
                'name' => null,
            ]);
        }

        $this->profile = json_decode($json);
    }

    /**
     * @param null $json
     * @return static
     */
    public static function instance($json = null)
    {
        if (!isset(self::$instance))
            self::$instance = new static($json);

        return self::$instance;
    }

    public function id()
    {
        return $this->profile->id;
    }

    public function name()
    {
        return $this->profile->name;
    }
}