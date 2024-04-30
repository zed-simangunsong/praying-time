<?php
/**
 * Copyright (c) 2024. by zed-simangunsong
 *
 * @license     MIT License
 * @copyright   Copyright (2) 2024, zed-simangunsong
 */

namespace Zed\Test\Lib;


class Route
{
    /**
     * @var string Controller
     */
    protected $controller;

    /**
     * @var string Action
     */
    protected $action = 'indexAction';

    /**
     * @var array
     */
    protected $segment = [];

    public function __construct()
    {
        // Set queries.
        $queries = array_map('str_escape', $_GET);

        // Set controller.
        $this->controller = env('BASE_CONTROLLER', 'Zed\Test\App\Controllers\IndexController');

        if (isset($queries['playground_controller'])) {
            $this->controller = 'Zed\Test\App\Controllers\\'
                . Str::camelize($queries['playground_controller'], '.')
                . 'Controller';
        }

        // Set action & segment.
        $this->action = env('BASE_ACTION', $this->action);
        if (isset($queries['playground_segment']) && !empty($queries['playground_segment'])) {
            $this->segment = explode('/', trim($queries['playground_segment'], '/'));

            $this->action = Str::camelcase(array_shift($this->segment)) . 'Action';
        }
    }

    /**
     * Execute the request,and return the response.
     *
     * @return mixed
     */
    public function response()
    {
        if (!class_exists($this->controller)) {
            return $this->error('Page not found.', 404);
        } else {
            return (new $this->controller)->{$this->action}(...$this->segment);
        }
    }

    protected function error($context = '', $header = '404')
    {
        return $header . ' ' . $context;
    }
}