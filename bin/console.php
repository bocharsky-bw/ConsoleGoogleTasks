#!/usr/bin/env php
<?php

/**
 * @link https://developers.google.com/google-apps/tasks/quickstart/php#step_2_install_the_google_client_library
 */

require __DIR__.'/../vendor/autoload.php';

use AlVi\Application;
use Pimple\Container;

$container = new Container();
$container['google_client'] = function () {
    $client = new Google_Client();

    // configure google client
    $client->setApplicationName('Console Google Tasks');
    $client->setAccessType('offline');
    $client->setAuthConfigFile(__DIR__.'/../client_secret.json');
    $client->setScopes([
        \Google_Service_Tasks::TASKS_READONLY,
    ]);

    return $client;
};
$container['google_service_tasks'] = function (Container $c) {
    return new Google_Service_Tasks($c['google_client']);
};

$app = new Application();
$app->setContainer($container);

// Register commands
$app->addCommands([
    new \AlVi\Command\App\ConfigureCommand(),
    new \AlVi\Command\Task\ListCommand(),
    new \AlVi\Command\TaskList\ListCommand(),
]);

$app->run();
