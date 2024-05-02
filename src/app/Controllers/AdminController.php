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
use Zed\Test\Lib\User;

class AdminController
{
    protected $admin;

    public function __construct()
    {
        $this->admin = new User($_SESSION['subscriber'] ?? null);
    }

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

        // Default active zone.
        if (!$activeZone && isset($zones[0])) $activeZone = $zones[0];

        // Get boxes by zone.
        $boxes = $activeZone ? BoxModel::instance()->getByZonePrayerTimeOption($activeZone) : [];

        return view('index.twig', [
            'zones' => $zones,
            'activeZone' => $activeZone,
            'boxes' => $boxes,
            'basePage' => BASE_URL . '/admin.html/index',
        ]);
    }
}