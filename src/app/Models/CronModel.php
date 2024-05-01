<?php
/**
 * Copyright (c) 2024. by zed-simangunsong
 *
 * @license     MIT License
 * @copyright   Copyright (2) 2024, zed-simangunsong
 */

namespace Zed\Test\App\Models;


class CronModel extends BaseModel
{
    protected $table = 'cron';

    /**
     * Check if given date already have executed cron task.
     *
     * @param $date
     * @param $box_id
     * @param $zone
     * @return \stdClass|string|null
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
    public function haveCronTask($date, $box_id, $zone)
    {
        return $this->builder()
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->where('box_id', '<=', $box_id)
            ->where('prayer_zone', '>=', $zone)
            ->first();
    }
}
