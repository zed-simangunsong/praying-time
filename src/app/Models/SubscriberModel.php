<?php
/**
 * Copyright (c) 2024. by zed-simangunsong
 *
 * @license     MIT License
 * @copyright   Copyright (2) 2024, zed-simangunsong
 */

namespace Zed\Test\App\Models;


class SubscriberModel extends BaseModel
{
    protected $table = 'subscriber';

    public function box($subscriber_id, $prayerTimeOption = 1)
    {
        $builder = $this
            ->builder()
            ->select('subscriber.subscriber_id', 'box.prayer_zone', 'box.box_id', 'box.box_name')
            ->joinUsing('subscriber_box', 'subscriber_id')
            ->joinUsing('box', 'box_id');

        is_array($subscriber_id)
            ? $builder->whereIn('subscriber.subscriber_id', $subscriber_id)
            : $builder->where('subscriber.subscriber_id', $subscriber_id);

        if (!is_null($prayerTimeOption)) {
            $builder->where('box.prayer_time_option', $prayerTimeOption);
        }

        return $builder->orderBy('box.prayer_zone')
            ->get();
    }
}