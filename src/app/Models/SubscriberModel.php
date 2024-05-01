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

    public function box($subscriber_id)
    {
        return $this
            ->builder()
            ->select('subscriber.subscriber_id', 'box.prayer_zone', 'box.box_id')
            ->joinUsing('subscriber_box', 'subscriber_id')
            ->joinUsing('box', 'box_id')
            ->findAll('subscriber.subscriber_id', $subscriber_id);
    }
}