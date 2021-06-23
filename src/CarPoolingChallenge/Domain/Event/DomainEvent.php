<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Event;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

interface DomainEvent extends \JsonSerializable
{
    public function id(): UuidInterface;

    public function correlationId(): UuidInterface;

    /**
     * @return DateTimeInterface
     */
    public function occurredOn(): DateTimeInterface;
}
