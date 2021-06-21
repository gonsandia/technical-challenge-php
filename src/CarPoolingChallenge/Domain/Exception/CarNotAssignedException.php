<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Exception;

use Throwable;

class CarNotAssignedException extends DomainException
{
    public function __construct($message = 'There is not empty car for this group.', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
