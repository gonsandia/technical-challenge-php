<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Model;

use DateTimeInterface;
use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEvent;

class JourneyPerformed implements DomainEvent
{
    private JourneyId $journeyId;

    private TotalPeople $totalPeople;

    private ?CarId $carId = null;

    private DateTimeInterface $occurredOn;

    public function __construct(JourneyId $journeyId, TotalPeople $totalPeople, ?CarId $carId, DateTimeInterface $dateTime)
    {
        $this->journeyId = $journeyId;
        $this->totalPeople = $totalPeople;
        $this->carId = $carId;
        $this->occurredOn = $dateTime;
    }

    public static function from(Journey $journey): self
    {
        return new self($journey->getJourneyId(), $journey->getTotalPeople(), $journey->getCarId(), new \DateTimeImmutable());
    }

    /**
     * @return JourneyId
     */
    public function getJourneyId(): JourneyId
    {
        return $this->journeyId;
    }

    /**
     * @return TotalPeople
     */
    public function getTotalPeople(): TotalPeople
    {
        return $this->totalPeople;
    }

    /**
     * @return CarId|null
     */
    public function getCarId(): ?CarId
    {
        return $this->carId;
    }

    public function occurredOn(): DateTimeInterface
    {
        return $this->occurredOn;
    }

    public function jsonSerialize()
    {
        return json_encode([
            'car_id' => $this->carId,
            'people' => $this->totalPeople,
            'journey_id' => $this->journeyId,
            'occurred_on' => $this->occurredOn->format(DATE_ATOM),
            'type' => __CLASS__
        ], JSON_THROW_ON_ERROR);
    }
}
