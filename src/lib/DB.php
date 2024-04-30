<?php
/**
 * Copyright (c) 2024. by zed-simangunsong
 *
 * @license     MIT License
 * @copyright   Copyright (2) 2024, zed-simangunsong
 */

namespace Zed\Test\Lib;


use Pecee\Pixie\Connection;

class DB
{
    /**
     * Connections instance.
     *
     * @var array
     */
    protected static $connections = [];

    /**
     * Get the DB connection based on env, or
     * create if not exists.
     *
     * @return Connection|null
     */
    public static function getOrCreateDefaultConnection()
    {
        if (!isset(self::$connections['default'])) {
            return self::createConnection(
                env('DB_HOST'),
                env('DB_USERNAME'),
                env('DB_PASSWORD'),
                env('DB_DATABASE'),
                env('DB_DRIVER'),
                'default'
            );
        }

        return self::getConnection();
    }

    /**
     * Create a new DB connection.
     *
     * @param $host
     * @param $username
     * @param $password
     * @param $db
     * @param $driver
     * @param $group
     * @param array $options
     * @return Connection
     */
    public static function createConnection(
        $host, $username, $password, $db, $driver, $group, array $options = [])
    {
        $config = $options + [
                'driver' => $driver,
                'host' => $host,
                'database' => $db,
                'username' => $username,
                'password' => $password,
                'charset' => env('DB_CHARSET', 'utf8mb4'),
                'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
                'prefix' => '',
            ];

        return self::$connections[$group] = new Connection($driver, $config);
    }

    /**
     * Get the connection.
     *
     * @param string $group
     * @return Connection|null
     */
    public static function getConnection($group = 'default')
    {
        return self::$connections[$group] ?? null;
    }

    /**
     * Return the query builder.
     *
     * @param string $group
     * @return \Pecee\Pixie\QueryBuilder\QueryBuilderHandler
     * @throws \Pecee\Pixie\Exception
     */
    public static function builder($group = 'default')
    {
        return self::getConnection($group)->getQueryBuilder();
    }
}