<?php
/**
 * Copyright (c) 2024. by zed-simangunsong
 *
 * @license     MIT License
 * @copyright   Copyright (2) 2024, zed-simangunsong
 */

namespace Zed\Test\Lib;


class Subscriber
{
    protected $profile;

    public function __construct($json)
    {
        if (empty($json)) {
            // Default empty profile.
            $json = json_encode([
                'id' => null,
                'subscriber_name' => null,
            ]);
        }

        $this->profile = json_decode($json);
    }

    public function id()
    {
        return $this->profile->id;
    }

    public function name()
    {
        return $this->profile->subscriber_name;
    }
}