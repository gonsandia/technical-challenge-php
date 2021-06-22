<?php

namespace Gonsandia\CarPoolingChallenge\Infrastructure\Framework;

use Gonsandia\CarPoolingChallenge\Application\Exception\ContentTypeException;
use Gonsandia\CarPoolingChallenge\Domain\Exception\CarNotExistsException;
use Gonsandia\CarPoolingChallenge\Domain\Exception\JourneyNotExistsException;
use Gonsandia\CarPoolingChallenge\Infrastructure\Exception\BodyDeserializationException;
use Gonsandia\CarPoolingChallenge\Infrastructure\Exception\InvalidContentTypeException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
            $exception instanceof BodyDeserializationException, $exception instanceof HttpException => Response::HTTP_BAD_REQUEST,
                $exception instanceof InvalidContentTypeException => Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
//            $exception instanceof AuthenticationException => Response::HTTP_UNAUTHORIZED,
//            $exception instanceof AccessDeniedException => Response::HTTP_FORBIDDEN,
            $exception instanceof CarNotExistsException, $exception instanceof JourneyNotExistsException => Response::HTTP_NOT_FOUND,
//            $exception instanceof DomainLogicException => Response::HTTP_NO_CONTENT,
            default => Response::HTTP_BAD_REQUEST,
        };

        $this->logger->error($exception->getMessage());

        $message = [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
        ];

        $json = '';

        $response = new Response($json, $statusCode);

        // sends the modified response object to the event
        $event->setResponse($response);
    }
}
