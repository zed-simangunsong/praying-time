<?php
/**
 * Copyright (c) 2024. by zed-simangunsong
 *
 * @license     MIT License
 * @copyright   Copyright (2) 2024, zed-simangunsong
 */

namespace Zed\Test\Lib;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class View
{
    protected static $instance;

    protected $renderer;

    public function __construct(Environment $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Instantiate View class.
     *
     * @return static
     */
    public static function instance()
    {
        if (!self::$instance) {
            $loader = new FilesystemLoader(VIEW_PATH);

            self::$instance = new static(new Environment($loader, 'true' !== env('CACHE_VIEW') ? [] : ['cache' => CACHE_VIEW]));
        }

        return self::$instance;
    }

    /**
     * @param $view
     * @param array $context
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render($view, array $context = [])
    {
        return $this->renderer->render($view, $context);
    }
}