<?php

namespace Gonsandia\Tests\CarPoolingChallenge\Mocks\Infrastructure\Persistence\InMemory\Repository;

use Gonsandia\CarPoolingChallenge\Domain\Exception\CarNotExistsException;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarId;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalPeople;

class InMemoryCarRepository implements CarRepository
{
    /**
     * @var Car[]
     */
    private $cars = array();

    public function save(Car $car): void
    {
        $this->cars[$car->getCarId()->value()] = $car;
    }

    public function findCarForPeople(TotalPeople $totalPeople): ?Car
    {
        foreach ($this->cars as $car) {
            if ($car->getAvailableSeats()->value() >= $totalPeople->value()) {
                return $car;
            }
        }

        return null;
    }

    public function ofJourneyId(JourneyId $journeyId): ?Car
    {
        foreach ($this->cars as $car) {
            foreach ($car->getJourneys() as $journey) {
                if ($journey->getJourneyId()->equals($journeyId)) {
                    return $car;
                }
            }
        }

        return null;
    }

    public function loadCars(array $cars)
    {
        $this->cars = $cars;
    }

    public function clearTable()
    {
        $this->cars = [];
    }

    public function ofId(CarId $carId): Car
    {
        foreach ($this->cars as $car) {
            if ($car->getCarId()->equals($carId)) {
                return $car;
            }
        }

        throw new CarNotExistsException();
    }
}
