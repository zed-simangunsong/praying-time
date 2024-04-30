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
use Zed\Test\Lib\Request;

$today = date('Y-m-d');

$cron = CronModel::instance();

$start = time();
$endDate = date('Y-m-d', strtotime('+7 day'));

foreach (ZoneModel::getCodes() as $zone) {
    echo $zone . '<br/>';

    // Get boxes by zone.
    $boxes = BoxModel::instance()->getBoxByZone($zone, 'id', 'box_name');

    foreach ($boxes as $box) {
        echo $zone . ' ' . $box->box_name . '<br/>';

        $task = $cron->haveCronTask($today, 1, $zone);

        if (!$task) {
            $request = new Request(env('API_URL') . env('API_ZONE_KEY') . '=' . $zone);
            $result = $request->get();

            if ('ok' === $result->status && isset($result->data->prayerTime)) {
                BoxSongModel::instance()->builder()->transaction(function (Transaction $transaction)
                use ($result, $box) {
                    dd($result->data->prayerTime);
                });
            } else {
                // Send email.
            }
        }
    }
}

echo '<pre>' . print_r([
        'start' => date('Y-m-d H:i:s', $start),
        'finish' => date('Y-m-d H:i:s', time()),
    ], true) . '</pre>';