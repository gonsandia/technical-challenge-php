<?php

namespace Gonsandia\CarPoolingChallenge\Infrastructure\Exception;

use Throwable;

class BodyDeserializationException extends InfrastructureException
{
    public function __construct($message = 'Cant deserialize body', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
