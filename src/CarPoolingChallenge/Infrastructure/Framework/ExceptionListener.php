<?php


namespace Gonsandia\CarPoolingChallenge\Infrastructure\Framework;

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
//            $exception instanceof InvalidArgumentException, $exception instanceof SerializationException, $exception instanceof AssertMessageException, $exception instanceof AssertPayloadException => Response::HTTP_BAD_REQUEST,
//            $exception instanceof AuthenticationException => Response::HTTP_UNAUTHORIZED,
//            $exception instanceof AccessDeniedException => Response::HTTP_FORBIDDEN,
//            $exception instanceof NotFoundHttpException => Response::HTTP_NOT_FOUND,
//            $exception instanceof DomainLogicException => Response::HTTP_CONFLICT,
//            $exception instanceof ContentTypeException => Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
            $exception instanceof HttpException => $exception->getStatusCode(),
            default => Response::HTTP_INTERNAL_SERVER_ERROR,
        };

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
