<?php

namespace Gonsandia\CarPoolingChallenge\Application\Exception;

use Throwable;

class ContentTypeException extends ApplicationException
{
    public function __construct($message = 'Invalid content type', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
