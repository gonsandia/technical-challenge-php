<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Exception;

use Throwable;

class CarNotExistsException extends DomainException
{
    public function __construct($message = 'The car not exists.', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
