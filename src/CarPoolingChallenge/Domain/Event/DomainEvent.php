<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Event;

use DateTimeInterface;

interface DomainEvent extends \JsonSerializable
{
    /**
     * @return DateTimeInterface
     */
    public function occurredOn(): DateTimeInterface;
}
