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
    /**
     * API URL.
     *
     * @var string
     */
    protected $url;

    /**
     * Call method.
     *
     * @var string
     */
    protected $method;

    /**
     * Parameter data need to be send to the API.
     *
     * @var array
     */
    protected $data = [];

    /**
     * API response, normally be in JSON.
     *
     * @var string
     */
    protected $response;

    public function __construct($zone, $method = 'GET', array $data = [])
    {
        $this->url = env('API_URL') . '&' . env('API_ZONE_KEY') . '=' . $zone;

        $this->method = $method;

        $this->data = $data;
    }

    /**
     * Execute the API call.
     *
     * @return $this
     */
    public function getPrayerTimes()
    {
        try {
            $this->response = Request::curl($this->url, $this->method, $this->data);
        } catch (\Exception $e) {
            $this->response = json_encode([
                'status' => 'KO',
                'errorMessage' => $e->getMessage(),
            ]);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return mixed
     */
    public function toObject()
    {
        return json_decode($this->response);
    }
}