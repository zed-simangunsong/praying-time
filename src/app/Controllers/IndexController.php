<?php
/**
 *  Copyright (c) 2024. by zed-simangunsong
 *
 * @license     MIT License
 * @copyright   Copyright (2) 2024, zed-simangunsong
 */

namespace Zed\Test\App\Controllers;


use Zed\Test\App\Models\BoxModel;
use Zed\Test\App\Models\ZoneModel;

class IndexController
{
    /**
     * @param null $activeZone
     * @return string
     * @throws \Pecee\Pixie\Exception
     * @throws \Pecee\Pixie\Exceptions\ColumnNotFoundException
     * @throws \Pecee\Pixie\Exceptions\ConnectionException
     * @throws \Pecee\Pixie\Exceptions\DuplicateColumnException
     * @throws \Pecee\Pixie\Exceptions\DuplicateEntryException
     * @throws \Pecee\Pixie\Exceptions\DuplicateKeyException
     * @throws \Pecee\Pixie\Exceptions\ForeignKeyException
     * @throws \Pecee\Pixie\Exceptions\NotNullException
     * @throws \Pecee\Pixie\Exceptions\TableNotFoundException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function indexAction($activeZone = null)
    {
        $zones = ZoneModel::getCodes();

        if (!$activeZone && isset($zones[0])) $activeZone = $zones[0];

        return view('index.twig', [
            'zones' => $zones,
            'activeZone' => $activeZone,
            'boxes' => $activeZone ? BoxModel::instance()->getByZonePrayerTimeOption($activeZone) : [],
        ]);
    }
}