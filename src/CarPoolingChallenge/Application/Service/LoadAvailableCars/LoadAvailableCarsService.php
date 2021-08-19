<?php

namespace Gonsandia\CarPoolingChallenge\Application\Service\LoadAvailableCars;

use Gonsandia\CarPoolingChallenge\Application\Service\ApplicationService;
use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEventPublisher;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarsLoaded;
use Symfony\Component\EventDispatcher\EventDispatcher;

class LoadAvailableCarsService implements ApplicationService
{
    public function __construct(
        private CarRepository $carRepository,
        private EventDispatcher   $eventDispatcher
    )
    {
    }

    public function execute($request = null)
    {
        $cars = $request->getCars();

        $this->carRepository->loadCars($cars);

        $event = CarsLoaded::from($cars);

        $this->eventDispatcher->dispatch($event);

        return $cars;
    }
}
