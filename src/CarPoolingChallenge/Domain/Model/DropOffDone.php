<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Model;

use DateTimeInterface;
use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEvent;
use Gonsandia\CarPoolingChallenge\Domain\UuidProvider;
use Ramsey\Uuid\UuidInterface;

class DropOffDone implements DomainEvent
{
    private ?CarId $carId;

    private DateTimeInterface $occurredOn;

    private JourneyId $journeyId;

    public function __construct(JourneyId $journeyId, DateTimeInterface $occurredOn, CarId $carId = null)
    {
        $this->carId = $carId;
        $this->occurredOn = $occurredOn;
        $this->journeyId = $journeyId;
    }

    public static function from(Journey $journey): self
    {
        return new self($journey->getJourneyId(), new \DateTimeImmutable(), $journey->getCarId());
    }

    /**
     * @return CarId|null
     */
    public function getCarId(): ?CarId
    {
        return $this->carId;
    }

    /**
     * @return JourneyId
     */
    public function getJourneyId(): JourneyId
    {
        return $this->journeyId;
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
            'journey_id' => $this->journeyId,
            'car_id' => $this->carId,
            'occurred_on' => $this->occurredOn->format(DATE_ATOM),
            'type' => DropOffDone::class
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
