<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Model;

use Gonsandia\CarPoolingChallenge\Domain\Event\TriggerEventsTrait;
use Gonsandia\CarPoolingChallenge\Domain\Exception\CantDropOffException;

class Car implements AggregateRoot
{
    use TriggerEventsTrait;

    private CarId $carId;

    private TotalSeats $totalSeats;

    private AvailableSeats $availableSeats;

    private array $journeys = [];

    private function __construct(CarId $carId, TotalSeats $totalSeats, array $journeys = [])
    {
        $this->carId = $carId;
        $this->totalSeats = $totalSeats;
        $this->setJourneys($journeys);
    }

    public static function from(CarId $carId, TotalSeats $totalSeats, array $journeys = [])
    {
        return new self($carId, $totalSeats, $journeys);
    }

    public function getCarId(): CarId
    {
        return $this->carId;
    }

    /**
     * @return TotalSeats
     */
    public function getTotalSeats(): TotalSeats
    {
        return $this->totalSeats;
    }

    /**
     * @return Journey[]
     */
    public function getJourneys(): array
    {
        return $this->journeys;
    }

    /**
     * @return AvailableSeats
     */
    public function getAvailableSeats(): AvailableSeats
    {
        return $this->availableSeats;
    }

    public function performJourney(Journey $journey): void
    {
        $this->checkOrThrowPerformAction($this->totalSeats, $this->getJourneys(), $journey);

        $journey->assignCarId($this->carId);
        $this->addJourney($journey);
        $this->setAvailableSeats($this->totalSeats, $this->getJourneys());

        $this->trigger(JourneyPerformed::from($journey));
    }

    public function dropOff(Journey $journey): void
    {
        $this->checkOrThrowDropOffAction($this->getJourneys(), $journey);

        $journey->assignCarId(null);
        $this->removeJourney($journey);
        $this->setAvailableSeats($this->totalSeats, $this->getJourneys());

        $this->trigger(DropOffDone::from($journey));
    }

    private function setAvailableSeats(TotalSeats $totalSeats, array $journeys): void
    {
        $this->availableSeats = $this->calculateAvailableSeats($totalSeats, $journeys);
    }

    private function calculateAvailableSeats(TotalSeats $totalSeats, array $journeys): AvailableSeats
    {
        $usedSeats = 0;

        foreach ($journeys as $journey) {
            $usedSeats += $journey->getTotalPeople()->value();
        }
        return new AvailableSeats($totalSeats->value() - $usedSeats);
    }

    private function checkOrThrowPerformAction(TotalSeats $totalSeats, array $journeys, Journey $journey): void
    {
        $tempJourney = $journeys;
        $tempJourney[$journey->getJourneyId()->value()] = $journey;

        $this->calculateAvailableSeats($totalSeats, $tempJourney);
    }

    private function checkOrThrowDropOffAction(array $journeys, Journey $journey): void
    {
        if (!array_key_exists($journey->getJourneyId()->value(), $journeys)) {
            throw new CantDropOffException();
        }
    }

    private function addJourney(Journey $journey): void
    {
        $this->journeys[$journey->getJourneyId()->value()] = $journey;
    }

    private function removeJourney(Journey $journey): void
    {
        unset($this->journeys[$journey->getJourneyId()->value()]);
    }

    public function setJourneys(array $journeys): void
    {
        $this->journeys = [];

        foreach ($journeys as $journey) {
            $this->addJourney($journey);
        }

        $this->setAvailableSeats($this->getTotalSeats(), $journeys);
    }
}
