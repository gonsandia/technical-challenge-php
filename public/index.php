<?php

use Gonsandia\CarPoolingChallenge\Infrastructure\Symfony\Kernel;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__) . '/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);

// create a log channel for domain events
$logger = new Logger('events');
$logger->pushHandler(new StreamHandler($kernel->getLogDir() . '/domain-events.log', Logger::INFO));

$request = Request::createFromGlobals();

$response = $kernel->handle($request);

$response->send();
$kernel->terminate($request, $response);
