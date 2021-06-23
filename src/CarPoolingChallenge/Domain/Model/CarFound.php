<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Model;

use DateTimeInterface;
use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEvent;
use Gonsandia\CarPoolingChallenge\Domain\UuidProvider;
use Ramsey\Uuid\UuidInterface;

class CarFound implements DomainEvent
{
    private ?CarId $carId;

    private JourneyId $journeyId;

    private DateTimeInterface $occurredOn;

    public function __construct(JourneyId $journeyId, DateTimeInterface $occurredOn, CarId $carId = null)
    {
        $this->carId = $carId;
        $this->journeyId = $journeyId;
        $this->occurredOn = $occurredOn;
    }

    public static function from(JourneyId $journeyId, Car $car = null): self
    {
        $carId = is_null($car) ? null : $car->getCarId();
        return new self($journeyId, new \DateTimeImmutable(), $carId);
    }


    public function occurredOn(): DateTimeInterface
    {
        return $this->occurredOn;
    }

    public function jsonSerialize()
    {
        return json_encode([
            'id' => $this->id(),
            'correlation_id' => $this->correlationId(),
            'car_id' => $this->carId,
            'journey_id' => $this->journeyId,
            'occurred_on' => $this->occurredOn->format(DATE_ATOM),
            'type' => __CLASS__
        ], JSON_THROW_ON_ERROR);
    }

    public function id(): UuidInterface
    {
        return UuidProvider::instance()->getUUID();
    }

    public function correlationId(): UuidInterface
    {
        return UuidProvider::instance()->getId();
    }
}
