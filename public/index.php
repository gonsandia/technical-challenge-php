<?php

use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEventPublisher;
use Gonsandia\CarPoolingChallenge\Domain\Event\LoggerDomainEventSubscriber;
use Gonsandia\CarPoolingChallenge\Infrastructure\Framework\Kernel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__) . '/vendor/autoload.php';

//return function (array $context) {
//
//    $kernel = new Kernel($context['APP_ENV'], (bool)$context['APP_DEBUG']);
//
//    DomainEventPublisher::instance()->subscribe(
//        new LoggerDomainEventSubscriber(
//            $kernel->getContainer()->get(LoggerInterface::class)
//        )
//    );
//
//    return $kernel;
//};

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();

$response = $kernel->handle($request);

$response->send();
$kernel->terminate($request, $response);
