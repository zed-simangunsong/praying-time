<?php
/*
|--------------------------------------------------------------------------
| Bootstrap the application.
|--------------------------------------------------------------------------
*/
require '../src/bootstrap.php';


use Pecee\Pixie\QueryBuilder\Transaction;
use Zed\Test\App\Models\BoxModel;
use Zed\Test\App\Models\BoxSongModel;
use Zed\Test\App\Models\CronModel;
use Zed\Test\App\Models\ZoneModel;
use Zed\Test\Lib\Api;

// Start date of praying time batch.
$today = date('Y-m-d');

$boxModel = BoxModel::instance();
$cronModel = CronModel::instance();
$boxSongModel = BoxSongModel::instance();


foreach (ZoneModel::getCodes() as $zone) {
    echo $zone . '<br/>';

    // Get boxes which have prayer time option on within zone.
    $boxes = $boxModel->getByZonePrayerTimeOption($zone, 1, 'id', 'box_name');

    foreach ($boxes as $box) {
        // Check if current date already have executed cron task.
        $task = $cronModel->haveCronTask($today, $box->id, $zone);

        if (!$task) {
            // Call the API and get prayer times.
            $request = (new Api($zone))->getPrayerTimes();

            if ('OK!' === $request->status && isset($request->prayerTime)) {
                if ([] !== $boxSongModel->insertBatchApi($today, $request->prayerTime, $box->id, $zone, $endDate)) {
                    // Batch inserted, now log to the cron table.
                    $cronModel->builder()->insert([
                        'start_date' => $today,
                        'end_date' => $endDate,
                        'box_id' => $box->id,
                        'prayer_zone' => $zone,
                    ]);
                }
            } else {
                // Send email regarding the error.
            }
        }
    }
}