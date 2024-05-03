<?php
/**
 * Copyright (c) 2024. by zed-simangunsong
 *
 * @license     MIT License
 * @copyright   Copyright (2) 2024, zed-simangunsong
 */

namespace Zed\Test\Lib;


use PHPMailer\PHPMailer\PHPMailer;

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
        $this->controller = env('BASE_CONTROLLER', 'Zed\Test\App\Controllers\AdminController');

        if (isset($queries['playground_controller'])) {
            $this->controller = 'Zed\Test\App\Controllers\\'
                . ucfirst(Str::camelCase($queries['playground_controller'], ['-', '_', '.']))
                . 'Controller';
        }

        // Set action & segment.
        $this->action = env('BASE_ACTION', $this->action);
        if (isset($queries['playground_segment']) && !empty($queries['playground_segment'])) {
            $this->segment = explode('/', trim($queries['playground_segment'], '/'));

            $this->action = Str::camelCase(array_shift($this->segment), ['-', '_', '.']) . 'Action';
        }
    }

    /**
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function response()
    {
        if (!class_exists($this->controller)) {
            return self::error('Page not found.', 404);
        } else {
            $page = new $this->controller;

            // Authorization.
            if (method_exists($page, 'authorize')) {
                if (true !== ($authorize = $page->authorize($this->action))) {
                    return $authorize;
                }
            }

            if (method_exists($page, $this->action)) {
                return $page->{$this->action}(...$this->segment);
            }

            return self::error('Page not found.', 404);
        }
    }

    /**
     * @param string $context
     * @param string $httpResponseCode
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public static function error($context = '', $httpResponseCode = '404')
    {
        return view('general/error-page.twig', [
            'context' => $context,
            'httpCode' => $httpResponseCode,
        ]);
    }
}