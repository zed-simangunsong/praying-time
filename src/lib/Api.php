<?php
/**
 * Copyright (c) 2024. by zed-simangunsong
 *
 * @license     MIT License
 * @copyright   Copyright (2) 2024, zed-simangunsong
 */

namespace Zed\Test\Lib;


class Api
{
    protected $url;

    protected $method;

    protected $data = [];

    public function __construct($zone, $method = 'GET', array $data = [])
    {
        $this->url = env('API_URL') . '&' . env('API_ZONE_KEY') . '=' . $zone;

        $this->method = $method;

        $this->data = $data;
    }


    public function getPrayerTimes()
    {
        try {
            $response = json_decode(Request::curl($this->url, $this->method, $this->data), true);
        } catch (\Exception $e) {
            $response = [
                'status' => 'KO',
                'data' => $e->getMessage(),
            ];
        }

        return json_decode(json_encode($response));
    }
}