<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Model;

class Journey
{
    private JourneyId $journeyId;

    private TotalPeople $totalPeople;

    private ?CarId $carId = null;

    /**
     * Journey constructor.
     * @param JourneyId $journeyId
     * @param TotalPeople $totalPeople
     * @param CarId|null $carId
     */
    private function __construct(JourneyId $journeyId, TotalPeople $totalPeople, ?CarId $carId)
    {
        $this->journeyId = $journeyId;
        $this->totalPeople = $totalPeople;
        $this->carId = $carId;
    }

    public static function from(JourneyId $journeyId, TotalPeople $totalPeople, ?CarId $carId = null): self
    {
        return new self($journeyId, $totalPeople, $carId);
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

    public function assignCarId(?CarId $carId): void
    {
        $this->carId = $carId;
    }


    public function __toString()
    {
        return serialize($this);
    }
}
