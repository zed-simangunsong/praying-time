<?php
/*
|--------------------------------------------------------------------------
| System directory.
|--------------------------------------------------------------------------
|
*/
define('VIEW_PATH', __DIR__ . '/../src/app/Views/');

/*
|--------------------------------------------------------------------------
| Load composer autoload.
|--------------------------------------------------------------------------
*/
include __DIR__ . '/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Load environment configurations.
|--------------------------------------------------------------------------
*/
$env = Dotenv\Dotenv::createImmutable(__DIR__ . DIRECTORY_SEPARATOR . '..');
$env->load();

use Zed\Test\App\Models\BoxModel;
use Zed\Test\App\Models\BoxSongModel;
use Zed\Test\App\Models\CronModel;
use Zed\Test\App\Models\ZoneModel;
use Zed\Test\Lib\Api;
use Zed\Test\Lib\Mailer;

// Start date of praying time batch.
$today = date('Y-m-d');

$boxModel = BoxModel::instance();
$cronModel = CronModel::instance();
$boxSongModel = BoxSongModel::instance();

$taskError = [];    // Failed box item.
$taskCounter = 0;   // Success batch insert BoxSongModel counter.

foreach (ZoneModel::getCodes() as $zone) {
    echo "Generating song for zone: $zone\n"; // Cli message.
    echo "-----------------------------------------------\n";

    // Get boxes which have prayer time option on within zone.
    $zoneBoxes = $boxModel->getByZonePrayerTimeOption($zone, 1, 'box_id', 'box_name');

    // Collect all boxes which song not generated yet.
    $boxes = [];
    foreach ($zoneBoxes as $box) {
        // Check if current date already have executed cron task.
        $task = $cronModel->haveCronTask($today, $box->box_id, $zone);

        if (!$task) {
            $boxes[] = $box;
        }
    }

    // Now lets do API call for that boxes;
    if ([] !== $boxes) {
        // Call the API and get prayer times.
        $apiRequest = (new Api($zone))->getPrayerTimes();
        $objResponse = $apiRequest->toObject();

        foreach ($boxes as $box) {
            echo "Generating song for box : " . $box->box_name; // Cli message.

            $error = '';
            if ($objResponse && 'OK!' === $objResponse->status && isset($objResponse->prayerTime)) {
                if ([] !== ($ids = $boxSongModel->insertBatchApi(
                        $today, $objResponse->prayerTime, $box->box_id, $zone, $endDate))) {

                    // Task increment.
                    $taskCounter++;

                    echo ' (' . count($ids) . " song generated)"; // Cli message.

                    // Batch inserted, now log to the cron table.
                    $cronModel->builder()->insert([
                        'start_date' => $today,
                        'end_date' => $endDate,
                        'box_id' => $box->box_id,
                        'prayer_zone' => $zone,
                        'last_update' => $today,
                    ]);
                } else {
                    echo ". \nError when trying insert data to box_song table."; // Cli message.

                    $error = 'Error when trying insert data to box_song table';
                }
            } else {
                if (isset($objResponse->prayerTime->data[0])) {
                    $error = $objResponse->prayerTime->data[0];
                } else {
                    $error = $objResponse->errorMessage;
                }
                echo ". \n$error"; // Cli message.
            }

            echo "\n"; //Cli message.

            // Error, collect the data.
            if ('' !== $error) {
                $taskError[] = [
                    'boxId' => $box->box_id,
                    'prayerZone' => $zone,
                    'errorMessage' => $error,
                    'apiResponse' => $apiRequest->getResponse(),
                ];
            }
        }
    } else {
        echo "All zone boxes already generated, or \nit might not have any available boxes.\n"; // Cli message.
    }

    echo "\n-----------------------------------------------\n";
}

// We have error, send the notification email.
if ([] !== $taskError) {
    $email = Mailer::instance()
        ->setTo(env('MAIL_CRON_ADDRESS', 'phu@expressinmusic.com'), env('MAIL_CRON_NAME', 'Phu'))
        ->setSubject(env('MAIL_CRON_ERROR_SUBJECT', 'Cron task error!'))
        ->send('mail/cron-error.twig', [
            'recipient' => env('MAIL_CRON_NAME', 'Phu'),
            'cronDate' => date('Y-m-d H:i:s'),
            'cronData' => $taskError,
        ]);

    $result = [
        'status' => 'KO',
        'message' => 'Email notification ' . ('OK' === $email['status'] ? '' : 'did not ') . 'sent!',
        'taskError' => serialize($taskError),
    ];
} elseif ($taskCounter > 0) {
    $result = [
        'status' => 'OK',
        'message' => $taskCounter . ' done',
    ];
} else {
    $result = [
        'status' => 'OK',
        'message' => 'No task need to do',
    ];
}

echo json_encode($result);