<?php

namespace Gonsandia\CarPoolingChallenge\Application\Service\LoadAvailableCars;

use Gonsandia\CarPoolingChallenge\Application\Service\ApplicationService;
use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEventPublisher;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarId;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarsLoaded;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalSeats;

class LoadAvailableCarsService implements ApplicationService
{
    private CarRepository $carRepository;

    /**
     * LoadAvailableCarsService constructor.
     * @param CarRepository $carRepository
     */
    public function __construct(CarRepository $carRepository)
    {
        $this->carRepository = $carRepository;
    }

    public function execute($request = null): LoadAvailableCarsResponse
    {
        $cars = $this->loadCarsFromArray($request->getCars());

        $this->carRepository->loadCars($cars);

        return new LoadAvailableCarsResponse($cars);
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

        DomainEventPublisher::instance()->publish(
            CarsLoaded::from($cars)
        );

        return $loadedCars;
    }
}
