<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Model;

use DateTimeInterface;
use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEvent;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class CarsLoaded implements DomainEvent
{
    private array $cars;

    private DateTimeInterface $occurredOn;

    public function __construct(array $cars, DateTimeInterface $occurredOn)
    {
        $this->cars = $cars;
        $this->occurredOn = $occurredOn;
    }

    public static function from(array $cars): self
    {
        return new self($cars, new \DateTimeImmutable());
    }

    public function occurredOn(): DateTimeInterface
    {
        return $this->occurredOn;
    }

    public function jsonSerialize()
    {
        return json_encode([
            'id' => $this->id(),
            'cars' => $this->cars,
            'journey_id' => $this->journeyId,
            'occurred_on' => $this->occurredOn->format(DATE_ATOM),
            'type' => __CLASS__
        ], JSON_THROW_ON_ERROR);
    }

    public function id(): UuidInterface
    {
        return Uuid::uuid4();
    }
}
