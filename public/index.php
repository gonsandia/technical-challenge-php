<?php

use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEventPublisher;
use Gonsandia\CarPoolingChallenge\Domain\Event\LoggerDomainEventSubscriber;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\UuidProvider;
use Gonsandia\CarPoolingChallenge\Infrastructure\Framework\Kernel;
use Gonsandia\CarPoolingChallenge\Infrastructure\Trace\RequestTracer;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__) . '/vendor/autoload.php';

//return function (array $context) {
//
//    $kernel = new Kernel($context['APP_ENV'], (bool)$context['APP_DEBUG']);
//

//
//    return $kernel;
//};

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);

// create a log channel for domain events
$logger = new Logger('events');
$logger->pushHandler(new StreamHandler($kernel->getLogDir() . '/domain-events.log', Logger::INFO));

DomainEventPublisher::instance()->subscribe(
   new LoggerDomainEventSubscriber($logger)
);

UuidProvider::instance();

$request = Request::createFromGlobals();

RequestTracer::instance()->setRequest($request);

$response = $kernel->handle($request);

$response->send();
$kernel->terminate($request, $response);
