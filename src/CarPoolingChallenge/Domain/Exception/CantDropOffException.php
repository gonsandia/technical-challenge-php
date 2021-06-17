<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Exception;

use Throwable;

class CantDropOffException extends DomainException
{
    public function __construct($message = 'Cant dropOff the journey.', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
