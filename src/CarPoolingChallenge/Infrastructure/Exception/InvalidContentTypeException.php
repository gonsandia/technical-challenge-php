<?php

namespace Gonsandia\CarPoolingChallenge\Infrastructure\Exception;

use Throwable;

class InvalidContentTypeException extends \Exception
{
    public function __construct($message = 'Invalid content type found.', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
