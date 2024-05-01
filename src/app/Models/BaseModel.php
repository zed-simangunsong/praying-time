<?php
/**
 * Copyright (c) 2024. by zed-simangunsong
 *
 * @license     MIT License
 * @copyright   Copyright (2) 2024, zed-simangunsong
 */

namespace Zed\Test\App\Models;


use Pecee\Pixie\Connection;
use Zed\Test\Lib\DB;

abstract class BaseModel
{
    /**
     * @var string
     */
    protected $table;

    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param Connection|null $connection
     * @return static
     */
    public static function instance(Connection $connection = null)
    {
        return new static($connection ?? DB::getOrCreateDefaultConnection());
    }

    /**
     * @param mixed ...$columns
     * @return \Pecee\Pixie\QueryBuilder\QueryBuilderHandler
     * @throws \Pecee\Pixie\Exception
     */
    public function select(...$columns)
    {
        $builder = $this->builder();

        if ([] !== $columns)
            $builder->select($columns);

        return $builder;
    }

    /**
     * @return \Pecee\Pixie\QueryBuilder\QueryBuilderHandler
     * @throws \Pecee\Pixie\Exception
     */
    public function builder()
    {
        return $this->connection->getQueryBuilder()->table($this->table);
    }

    /**
     * Reindex an array keyed by existing field.
     *
     * @param $rows
     * @param $keyField
     * @return array
     */
    public function keyBy($rows, $keyField)
    {
        $arr = [];

        foreach ($rows as $row) {
            $arr[$row->{$keyField}] = $row;
        }

        return $arr;
    }
}