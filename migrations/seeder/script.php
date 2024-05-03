<?php
/**
 * Copyright (c) 2024. by zed-simangunsong
 *
 * @license     MIT License
 * @copyright   Copyright (2) 2024, zed-simangunsong
 */

/*
|--------------------------------------------------------------------------
| Composer autoloader.
|--------------------------------------------------------------------------
*/
include 'vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Load environment configurations.
|--------------------------------------------------------------------------
*/
$env = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$env->load();

// Colors.
$colors = file_get_contents(__DIR__ . '/color.txt');
$colors = json_decode($colors, true);

// Words.
$words = file_get_contents(__DIR__ . '/word.txt');
$array = json_decode($words, true);
$words = [];
foreach ($array as $values) {
    $words = array_merge($words, $values);
}
shuffle($words);

$reserveColors = [];
$reserveWords = [];

function faker($colors, $words, &$reserveColors, &$reserveWords)
{
    while (true) {
        $colorIndex = rand(0, count($colors) - 1);
        $wordIndex = rand(0, count($words) - 1);

        if (!isset($reserveColors[$colorIndex]) && !isset($reserveWords[$wordIndex])) {
            $reserveColors[$colorIndex] = 1;
            $reserveWords[$wordIndex] = 1;

            return $colors[$colorIndex] . ' ' . ucfirst($words[$wordIndex]);
        }
    }
}


// Generate box names for each zones.
$boxes = [];

foreach (Zed\Test\App\Models\ZoneModel::getCodes() as $zone) {
    $boxes[] = [
        'box_name' => faker($colors, $words, $reserveColors, $reserveWords),
        'prayer_zone' => $zone,
        'prayer_time_option' => 1,
    ];
}

$boxModel = Zed\Test\App\Models\BoxModel::instance();
$newBoxIds = $boxModel->builder()->insert($boxes);

// Set last box prayer option to be false.
$boxModel->builder()->where('prayer_zone', $zone)->update(['prayer_time_option' => 0]);

// We do not want assign subscriber to last box.
array_pop($boxes);

echo count($newBoxIds) . " generated. Last box prayer option is false. \n";

// Generate 50 subscriber.
for ($i = 0; $i < 2; $i++) {
    $subscribers = [];
    for ($j = 0; $j < 25; $j++) {
        $subscribers[] = [
            'subscriber_name' => faker($colors, $words, $reserveColors, $reserveWords),
            'password' => password_hash('password', PASSWORD_DEFAULT),
        ];
    }

    $newSubscriberIds = Zed\Test\App\Models\SubscriberModel::instance()->builder()->insert($subscribers);
    echo count($newSubscriberIds) . " subscribers is generated. \n";

    // Subscribe random user to random box.
    $subscriberBox = [];
    $subscriberBoxes = [];

    for ($x = 1; $x <= 25; $x++) {
        $subscriberId = $newSubscriberIds[array_rand($newSubscriberIds)];
        $boxId = $newBoxIds[array_rand($newBoxIds)];

        // subscriber_id & box_id should be unique.
        if (!isset($subscriberBoxes[$subscriberId]) || !in_array($boxId, $subscriberBoxes[$subscriberId])) {
            $subscriberBoxes[$subscriberId][] = $boxId;

            $subscriberBox[] = ['subscriber_id' => $subscriberId, 'box_id' => $boxId];
        }

    }

    Zed\Test\App\Models\SubscriberBoxModel::instance()->builder()->insert($subscriberBox);
}





