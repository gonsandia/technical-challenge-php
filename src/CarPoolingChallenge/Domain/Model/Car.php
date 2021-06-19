<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Model;

use Gonsandia\CarPoolingChallenge\Domain\Exception\CantDropOffException;

class Car
{
    private CarId $carId;

    private TotalSeats $totalSeats;

    private AvailableSeats $availableSeats;

    private array $journeys;

    public function __construct(CarId $carId, TotalSeats $totalSeats, array $journeys = [])
    {
        $this->carId = $carId;
        $this->totalSeats = $totalSeats;


        foreach ($journeys as $journey) {
            $this->addJourney($journey);
        }

        $this->setAvailableSeats($totalSeats, $journeys);
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
     * @return array
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
        $this->checkOrThrowPerformAction($this->totalSeats, $this->journeys, $journey);

        $journey->assignCarId($this->carId);
        $this->addJourney($journey);
        $this->setAvailableSeats($this->totalSeats, $this->journeys);
    }

    public function dropOff(Journey $journey): void
    {
        $this->checkOrThrowDropOffAction($this->journeys, $journey);

        $journey->assignCarId(null);
        $this->removeJourney($journey);
        $this->setAvailableSeats($this->totalSeats, $this->journeys);
    }

    private function setAvailableSeats($totalSeats, $journeys): void
    {
        $this->availableSeats = self::calculateAvailableSeats($totalSeats, $journeys);
    }

    private static function calculateAvailableSeats(TotalSeats $totalSeats, array $journeys): AvailableSeats
    {
        $usedSeats = 0;
        foreach ($journeys as $journey) {
            $usedSeats += $journey->getTotalPeople()->getCount();
        }
        return new AvailableSeats($totalSeats->getCount() - $usedSeats);
    }

    private function checkOrThrowPerformAction(TotalSeats $totalSeats, array $journeys, Journey $journey): void
    {
        $tempJourney = $journeys;
        $tempJourney[$journey->getJourneyId()->getId()] = $journey;

        self::calculateAvailableSeats($totalSeats, $tempJourney);
    }

    private function checkOrThrowDropOffAction(array $journeys, Journey $journey): void
    {
        if (!array_key_exists($journey->getJourneyId()->getId(), $journeys)) {
            throw new CantDropOffException();
        }
    }

    private function addJourney(Journey $journey): void
    {
        $this->journeys[$journey->getJourneyId()->getId()] = $journey;
    }

    private function removeJourney(Journey $journey): void
    {
        unset($this->journeys[$journey->getJourneyId()->getId()]);
    }
}
