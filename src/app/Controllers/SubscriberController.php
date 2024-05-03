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
     * @param null $currentBox
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
    public function indexAction($currentBox = null)
    {
        if (!$this->subscriber->id()) {
            // Show login form.
            return $this->loginAction();
        } else {
            $boxes = $this->getSubscriberBox($currentBox);

            if (isset($boxes[$currentBox])) {
                // Get box song.
                $songs = $this->boxSongModel->getSongByBoxAndDate($boxes[$currentBox]->box_id, date('Y-m-d'));

                return view('subscriber/index.twig', [
                    'boxes' => $boxes,
                    'songs' => $songs,
                    'last_refresh' => $_SESSION['last_refresh'],
                    'autoPlay' => env('AUTO_PLAY', 'true'),
                    'hideButtonAfter' => env('HIDE_BIG_CARD_TIMER', 300000),
                    'basePage' => BASE_URL . '/subscriber.html/index',
                    'currentBox' => $currentBox,
                    'username' => $this->subscriber->name(),
                ]);
            } else {
                return view('subscriber/no-subscription.twig', [
                    'username' => $this->subscriber->name(),
                ]);
            }
        }
    }

    /**
     * Show login form.
     *
     * @param $username
     * @param $error
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function loginAction($username = '', $error = '')
    {
        if (isset($_SESSION['subscriber'])) {
            $this->redirectLoggedSubscriber();
        }

        return view('general/login.twig', [
            'formAction' => BASE_URL . '/subscriber.html/do-login',
            'usernamePlaceholder' => 'Subscriber name',
            'token' => $_SESSION['token'],
            'username' => urldecode($username),
            'error' => urldecode($error),
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
     * Get subscriber boxes.
     *
     * @param $currentBox
     * @return array
     */
    protected function getSubscriberBox(&$currentBox)
    {
        // Get subscriber boxes.
        $boxes = $this->subscriberModel->box($this->subscriber->id());
        $boxes = $this->subscriberModel->keyBy($boxes, 'box_id');

        foreach ($boxes as $box) {
            if (!isset($currentBox)) $currentBox = $box->box_id;
        }

        return $boxes;
    }

    /**
     * Handle login process.
     *
     * @return string|void
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
    public function doLoginAction()
    {
        $error = null;
        $username = '';

        if ($_POST) {
            $username = $_POST['username'];

            if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
                $error = 'Invalid CSRF token.';
            } else {
                // Find subscriber with subscriber name.
                $subscriber = SubscriberModel::instance()
                    ->builder()
                    ->find($username, 'subscriber_name');

                if (!($subscriber) || !password_verify($_POST['password'], $subscriber->password)) {
                    $error = 'Subscriber not found. Please check again.';
                } else {
                    // Set login session.
                    $_SESSION['subscriber'] = json_encode([
                        'id' => $subscriber->subscriber_id,
                        'name' => $subscriber->subscriber_name
                    ]);

                    $this->redirectLoggedSubscriber();

                    return;
                }
            }
        }

        return $this->loginAction($username, $error);
    }

    /**
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
    public function logoutAction()
    {
        $_SESSION['subscriber'] = null;

        return $this->loginAction();
    }
}