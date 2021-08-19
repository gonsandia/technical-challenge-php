<?php

namespace Gonsandia\CarPoolingChallenge\Application\Service\LoadAvailableCars;

use Gonsandia\CarPoolingChallenge\Application\Service\ApplicationService;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarId;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarsLoaded;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalSeats;
use Psr\EventDispatcher\EventDispatcherInterface;

class LoadAvailableCarsService implements ApplicationService
{
    public function __construct(
        private CarRepository $carRepository,
        private EventDispatcherInterface   $eventDispatcher
    ) {
    }

    public function execute($request = null)
    {
        $cars = $this->loadCarsFromArray($request['cars']);

        $this->carRepository->loadCars($cars);

        $event = CarsLoaded::from($cars);

        $this->eventDispatcher->dispatch($event);

        return $cars;
    }

    private function loadCarsFromArray(array $cars): array
    {
        $loadedCars = [];
        foreach ($cars as $car) {
            $loadedCars[$car['id']] = Car::from(
                new CarId($car['id']),
                new TotalSeats($car['seats'])
            );
        }

        return $loadedCars;
    }
}
