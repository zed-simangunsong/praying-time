<?php
/**
 * Copyright (c) 2024. by zed-simangunsong
 *
 * @license     MIT License
 * @copyright   Copyright (2) 2024, zed-simangunsong
 */

namespace Zed\Test\Lib;


class Request
{
    protected $url;

    protected $method;

    protected $data = [];

    protected $sequence = [];

    public function __construct($url, $method = 'GET', array $data = [])
    {
        $this->url = $url;

        $this->method = $method;

        $this->data = $data;
    }


    public function get()
    {
        try {
            $response = [
                'status' => 'ok',
                'data' => json_decode($this->execCurl()),
            ];
        } catch (\Exception $e) {
            $response = [
                'status' => 'ko',
                'data' => $e->getMessage(),
            ];
        }

        return json_decode(json_encode($response));
    }

    /**
     * Execute CURL.
     *
     * @return bool|string
     */
    protected function execCurl()
    {
        $curl = curl_init();

        switch ($this->method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($this->data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $this->data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($this->data)
                    $this->url = sprintf("%s?%s", $this->url, http_build_query($this->data));
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }
}