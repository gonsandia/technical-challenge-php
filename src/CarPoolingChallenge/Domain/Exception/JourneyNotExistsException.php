<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Exception;

use Throwable;

class JourneyNotExistsException extends DomainException
{
    public function __construct($message = 'The journey not exists.', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
