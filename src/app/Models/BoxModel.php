<?php
/**
 * Copyright (c) 2024. by zed-simangunsong
 *
 * @license     MIT License
 * @copyright   Copyright (2) 2024, zed-simangunsong
 */

namespace Zed\Test\App\Models;


class BoxModel extends BaseModel
{
    protected $table = 'box';

    /**
     * @param $whereZone
     * @param $selectColumns
     * @return array
     * @throws \Pecee\Pixie\Exception
     * @throws \Pecee\Pixie\Exceptions\ColumnNotFoundException
     * @throws \Pecee\Pixie\Exceptions\ConnectionException
     * @throws \Pecee\Pixie\Exceptions\DuplicateColumnException
     * @throws \Pecee\Pixie\Exceptions\DuplicateEntryException
     * @throws \Pecee\Pixie\Exceptions\DuplicateKeyException
     * @throws \Pecee\Pixie\Exceptions\ForeignKeyException
     * @throws \Pecee\Pixie\Exceptions\NotNullException
     * @throws \Pecee\Pixie\Exceptions\TableNotFoundException
     */
    public function getByZone($whereZone, ...$selectColumns)
    {
        return $this
            ->select(...$selectColumns)
            ->where('prayer_zone', $whereZone)
            ->get();
    }

    /**
     * @param $whereZone
     * @param int $wherePrayerTimeOption
     * @param mixed ...$selectColumns
     * @return array
     * @throws \Pecee\Pixie\Exception
     * @throws \Pecee\Pixie\Exceptions\ColumnNotFoundException
     * @throws \Pecee\Pixie\Exceptions\ConnectionException
     * @throws \Pecee\Pixie\Exceptions\DuplicateColumnException
     * @throws \Pecee\Pixie\Exceptions\DuplicateEntryException
     * @throws \Pecee\Pixie\Exceptions\DuplicateKeyException
     * @throws \Pecee\Pixie\Exceptions\ForeignKeyException
     * @throws \Pecee\Pixie\Exceptions\NotNullException
     * @throws \Pecee\Pixie\Exceptions\TableNotFoundException
     */
    public function getByZonePrayerTimeOption($whereZone, $wherePrayerTimeOption = 1, ...$selectColumns)
    {
        return $this
            ->select(...$selectColumns)
            ->where('prayer_zone', $whereZone)
            ->where('prayer_time_option', $wherePrayerTimeOption)
            ->get();
    }
}