<?php
/**
 * Copyright (c) 2024. by zed-simangunsong
 *
 * @license     MIT License
 * @copyright   Copyright (2) 2024, zed-simangunsong
 */

namespace Zed\Test\App\Models;


use Pecee\Pixie\QueryBuilder\Transaction;

class BoxSongModel extends BaseModel
{
    protected $table = 'box_song';

    protected $prayerTimes = [
        'imsak' => 'Imsak',
        'fajr' => 'Subuh',
        'syuruk' => 'Syuruk',
        'dhuhr' => 'Zohor',
        'asr' => 'Asar',
        'maghrib' => 'Maghrib',
        'isha' => 'Isyak',
    ];

    /**
     * Generate box song based on the batch API result.
     *
     * @param $startDate
     * @param array $batch
     * @param int $boxId
     * @param string $zone
     * @param $endDate
     * @return array|string
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
    public function insertBatchApi($startDate, array $batch, $boxId, $zone, &$endDate = null)
    {
        // We only want to get 7 days only from the batch.
        $endDate = date('Y-m-d', strtotime('+6 day', strtotime($startDate)));
        $endDateTime = strtotime($endDate);

        // Rows to be inserted in to DB.
        $rows = [];

        if ($batch) {
            $lastPrayerTimeSeq = (int)$this->getLastPrayerTimeSeq();

            foreach ($batch as $item) {
                // Do not insert all date which exceed the end date.
                if (strtotime($item->date) > $endDateTime) continue;

                foreach ($this->prayerTimes as $apiKey => $prayerName) {
                    $lastPrayerTimeSeq++;

                    $rows[] = [
                        'box_id' => $boxId,
                        'prayer_date' => date('Y-m-d', strtotime($item->date)),
                        'prayer_time' => $item->{$apiKey},
                        'song_title' => $this->generateSongTitle($prayerName, $item->date),
                        'prayer_time_seq' => $lastPrayerTimeSeq,
                        'audio_file_path' => $this->generateSongFileName($prayerName, $item->date, $zone),
                    ];
                }
            }

            // Use transaction for batch.
            return $this->builder()->insert($rows);
        }

        return [];
    }

    public function getLastPrayerTimeSeq()
    {
        return $this->builder()->max('prayer_time_seq');
    }

    protected function generateSongTitle($prayerName, $date)
    {
        return $prayerName . ' (' . date('m-d', strtotime($date)) . ')';
    }

    protected function generateSongFileName($prayerName, $date, $zone)
    {
        if ('false' !== strtolower(env('CRON_SONG_DEFAULT_FILE_PATH', 'false')))
            return env('CRON_SONG_DEFAULT_FILE_PATH');

        return '/songs/' . strtolower($prayerName) . '-' . date('m-d', strtotime($date)) . '-' . $zone . '.mp3';
    }

    /**
     * Get song by box and date.
     *
     * @param $boxId
     * @param $date
     * @param mixed ...$columns
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
    public function getSongByBoxAndDate($boxId, $date, ...$columns)
    {
        return $this
            ->select(...$columns)
            ->where('box_id', $boxId)
            ->where('prayer_date', $date)
            ->orderBy('prayer_time', 'ASC')
            ->get();

    }
}