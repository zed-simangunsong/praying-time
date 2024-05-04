<?php
/**
 * Copyright (c) 2024. by zed-simangunsong
 *
 * @license     MIT License
 * @copyright   Copyright (2) 2024, zed-simangunsong
 */
/**
 * --------------------------------------------------------------------------
 * Safely escape/encode the provided data.
 * --------------------------------------------------------------------------
 *
 * @param $string
 * @return string
 */
function str_escape($string)
{
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}

/**
 * Shortcut to Lib\View class, which using twig.
 *
 * @param $view
 * @param array $context
 * @return string
 * @throws \Twig\Error\LoaderError
 * @throws \Twig\Error\RuntimeError
 * @throws \Twig\Error\SyntaxError
 */
function view($view, array $context = [])
{
    return \Zed\Test\Lib\View::instance()->render($view, $context);
}

/**
 * Get environment variable.
 *
 * @param $key
 * @param null $default
 * @return mixed|null
 */
function env($key, $default = null)
{
    return $_ENV[$key] ?? $default;
}


