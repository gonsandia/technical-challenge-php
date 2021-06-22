<?php

namespace Gonsandia\CarPoolingChallenge\Application\Service\LoadAvailableCars;

use Gonsandia\CarPoolingChallenge\Application\Service\ApplicationService;
use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEventPublisher;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarsLoaded;

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
        $cars = $request->getCars();

        DomainEventPublisher::instance()->publish(
            CarsLoaded::from($cars)
        );

        $this->carRepository->loadCars($cars);

        return new LoadAvailableCarsResponse($cars);
    }
}
