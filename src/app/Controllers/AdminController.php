<?php
/**
 *  Copyright (c) 2024. by zed-simangunsong
 *
 * @license     MIT License
 * @copyright   Copyright (2) 2024, zed-simangunsong
 */

namespace Zed\Test\App\Controllers;


use Zed\Test\App\Models\BoxModel;
use Zed\Test\App\Models\BoxSongModel;
use Zed\Test\App\Models\SubscriberModel;
use Zed\Test\Lib\Paginator;
use Zed\Test\Lib\Route;
use Zed\Test\Lib\User;

class AdminController
{
    protected $admin;

    public function __construct()
    {
        $this->admin = User::instance($_SESSION['admin'] ?? null);
    }

    /**
     * This method automatically called by routing class.
     * If return value is bool true, then routing will be continued,
     * otherwise it will finished and display the return value.
     *
     * @param string $action Current URL action.
     * @return true|string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function authorize($action)
    {
        if (!in_array($action, ['loginAction', 'doLoginAction'])) {
            if (null === $this->admin->id()) {
                // Show login form.
                return $this->loginAction();
            } elseif ('admin' !== $this->admin->id()) {
                // Show unauthorized page.
                return Route::error('Unauthorized', 401);
            }
        }

        return true;
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
        if (isset($_SESSION['admin'])) {
            $this->redirectLoggedAdmin();
        }

        return view('general/login.twig', [
            'formAction' => BASE_URL . '/admin.html/do-login',
            'usernamePlaceholder' => 'Username',
            'token' => $_SESSION['token'],
            'username' => urldecode($username),
            'error' => urldecode($error),
        ]);
    }

    /**
     * Logged admin re-director.
     */
    protected function redirectLoggedAdmin()
    {
        header('Location: ' . BASE_URL . '/admin.html');
        exit;
    }

    /**
     * @param int $page
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
    public function indexAction($page = 1)
    {
        // Get subscribers.
        $model = SubscriberModel::instance();
        $builder = $model->builder();
        $paginator = (new Paginator(env('LISTING_PER_PAGE', 10), $builder))
            ->setPagingLink(BASE_URL . '/admin.html/index/:page')
            ->setCurrent($page)
            ->finalize();

        // Get subscriber box.
        $ids = $model->pluck($paginator->get('items', []), 'subscriber_id');

        $array = [];
        if ([] !== $ids) {
            $boxes = SubscriberModel::instance()->box($ids, null);
            foreach ($boxes as $box) $array[$box->subscriber_id][] = $box->box_name . ' - ' . $box->prayer_zone;
        }

        return $paginator
            ->view('admin/index.twig', [
                'basePage' => BASE_URL . '/admin.html',
                'username' => $this->admin->name(),
                'boxes' => $array,
            ]);
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
                if (!password_verify($_POST['password'], env('ADMIN_HASH'))) {
                    $error = 'Subscriber not found. Please check again.';
                } else {
                    // Set login session.
                    $_SESSION['admin'] = json_encode([
                        'id' => 'admin',
                        'name' => 'Admin',
                    ]);

                    $this->redirectLoggedAdmin();

                    return;
                }
            }
        }

        return $this->loginAction($username, $error);
    }

    /**
     * @param int $page
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
    public function boxAction($page = 1)
    {
        // Get subscribers.
        $builder = BoxModel::instance()->builder();
        $paginator = (new Paginator(env('LISTING_PER_PAGE', 10), $builder))
            ->setPagingLink(BASE_URL . '/admin.html/box/:page')
            ->setCurrent($page)
            ->finalize();

        return $paginator
            ->view('admin/box.twig', [
                'basePage' => BASE_URL . '/admin.html',
                'username' => $this->admin->name(),
            ]);
    }

    /**
     * @param int $page
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
    public function boxSongAction($page = 1)
    {
        // Get subscribers.
        $builder = BoxSongModel::instance()->builder();
        $paginator = (new Paginator(env('LISTING_PER_PAGE', 10), $builder))
            ->setPagingLink(BASE_URL . '/admin.html/box-song/:page')
            ->setCurrent($page)
            ->finalize();

        // Get song box.
        $ids = BoxSongModel::instance()->pluck($paginator->get('items', []), 'box_id');

        $array = [];
        if ([] !== $ids) {
            $boxes = BoxModel::instance()->builder()
                ->select('box_id', 'box_name', 'prayer_zone')
                ->whereIn('box_id', $ids)
                ->get();

            foreach ($boxes as $box) $array[$box->box_id][] = $box->box_name . ' - ' . $box->prayer_zone;
        }

        return $paginator
            ->view('admin/song.twig', [
                'basePage' => BASE_URL . '/admin.html',
                'username' => $this->admin->name(),
                'boxes' => $array,
            ]);
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
        $_SESSION['admin'] = null;

        return $this->loginAction();
    }
}