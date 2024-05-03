<?php

/**
 * This file will be used if you decide to use Doctrine Migrations,
 * to manage the Database migration.
 *
 * See https://www.doctrine-project.org/projects/doctrine-migrations/en/3.7/reference/introduction.html
 * for more detail.
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
$env = Dotenv\Dotenv::createImmutable(__DIR__);
$env->load();

use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;

$config = new PhpFile('migrations.php'); // Or use one of the Doctrine\Migrations\Configuration\Configuration\* loaders

$conn = DriverManager::getConnection([
    'dbname' => env('DB_DATABASE', 'main'),
    'user' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
    'host' => env('DB_HOST'),
    'driver' => env('DB_DRIVER_MIGRATION', 'pdo_mysql'),
    'memory' => true,
]);

return DependencyFactory::fromConnection($config, new ExistingConnection($conn));


