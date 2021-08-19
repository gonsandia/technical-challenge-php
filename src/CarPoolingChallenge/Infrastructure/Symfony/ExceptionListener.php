<?php

namespace Gonsandia\CarPoolingChallenge\Infrastructure\Symfony;

use Assert\InvalidArgumentException;
use Gonsandia\CarPoolingChallenge\Domain\Exception\CarNotExistsException;
use Gonsandia\CarPoolingChallenge\Domain\Exception\JourneyNotExistsException;
use Gonsandia\CarPoolingChallenge\Infrastructure\Exception\BodyDeserializationException;
use Gonsandia\CarPoolingChallenge\Infrastructure\Exception\InvalidContentTypeException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class ExceptionListener
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();

        $statusCode = match (true) {
            $exception instanceof InvalidArgumentException, $exception instanceof BodyDeserializationException, $exception instanceof MethodNotAllowedHttpException => Response::HTTP_BAD_REQUEST,
            $exception instanceof InvalidContentTypeException => Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
            $exception instanceof CarNotExistsException, $exception instanceof JourneyNotExistsException => Response::HTTP_NOT_FOUND,
            default => Response::HTTP_INTERNAL_SERVER_ERROR,
        };

        $message = [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'type' => get_class($exception)
        ];

        $json = json_encode($message, JSON_THROW_ON_ERROR);

        $this->logger->error($json);

        $response = new Response('', $statusCode);

        // sends the modified response object to the event
        $event->setResponse($response);
    }
}
