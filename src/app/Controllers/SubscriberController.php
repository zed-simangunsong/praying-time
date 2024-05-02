<?php
/**
 * Copyright (c) 2024. by zed-simangunsong
 *
 * @license     MIT License
 * @copyright   Copyright (2) 2024, zed-simangunsong
 */

namespace Zed\Test\App\Controllers;


use Zed\Test\App\Models\BoxSongModel;
use Zed\Test\App\Models\SubscriberModel;
use Zed\Test\Lib\User;

class SubscriberController
{
    /**
     * @var User
     */
    protected $subscriber;

    /**
     * @var SubscriberModel
     */
    protected $subscriberModel;

    /**
     * @var BoxSongModel
     */
    protected $boxSongModel;

    public function __construct()
    {
        $this->subscriber = new User($_SESSION['subscriber'] ?? null);

        $this->subscriberModel = SubscriberModel::instance();

        $this->boxSongModel = BoxSongModel::instance();
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
        if (!$this->subscriber->id()) {
            // Show login form.
            return $this->loginAction();
        } else {
            [$zones, $boxes] = $this->getSubscriberZones($activeZone);

            if ([] !== $zones || !isset($boxes[$activeZone])) {
                // Get box song.
                $songs = $this->boxSongModel->getSongByBoxAndDate($boxes[$activeZone]->box_id, date('Y-m-d'));

                return view('subscriber/index.twig', [
                    'zones' => $zones,
                    'songs' => $songs,
                    'last_refresh' => $_SESSION['last_refresh'],
                    'autoPlay' => env('AUTO_PLAY', 'true'),
                    'hideButtonTimer' => env('HIDE_BUTTON_AFTER', 120000),
                    'basePage' => BASE_URL . '/subscriber.html/index',
                    'activeZone' => $activeZone,
                ]);
            } else {
                // Show subscribe notification page.
            }
        }
    }

    /**
     * Show/Process login.
     *
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
    public function loginAction()
    {
        if (isset($_SESSION['subscriber'])) {
            $this->redirectLoggedSubscriber();
        }

        $error = null;
        $subscriberName = '';

        if ($_POST) {
            $subscriberName = $_POST['subscriberName'];

            if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
                $error = 'Invalid CSRF token.';
            } else {
                // Find subscriber with subscriber name.
                $subscriber = SubscriberModel::instance()
                    ->builder()
                    ->find($_POST['subscriberName'], 'subscriber_name');

                if (!($subscriber) | !password_verify($_POST['password'], $subscriber->password)) {
                    $error = 'Subscriber not found. Please check again.';
                } else {
                    // Set login session.
                    $_SESSION['subscriber'] = json_encode([
                        'id' => $subscriber->subscriber_id,
                        'subscriber_name' => $subscriber->subscriber_name
                    ]);

                    $this->redirectLoggedSubscriber();
                }
            }
        }

        return view('subscriber/login.twig', [
            'token' => $_SESSION['token'],
            'error' => $error,
            'subscriber' => $subscriberName,
        ]);
    }

    /**
     * Logged subscriber re-director.
     */
    protected function redirectLoggedSubscriber()
    {
        header('Location: ' . BASE_URL . '/subscriber.html');
        exit;
    }

    /**
     * @param $activeZone
     * @return array
     */
    protected function getSubscriberZones(&$activeZone)
    {
        // Get subscriber boxes.
        $zones = [];
        $boxes = $this->subscriberModel->box($this->subscriber->id());
        $boxes = $this->subscriberModel->keyBy($boxes, 'prayer_zone');

        foreach ($boxes as $box) {
            if (!isset($activeZone)) $activeZone = $box->prayer_zone;

            $zones[] = $box->prayer_zone;
        }

        return [$zones, $boxes];
    }
}