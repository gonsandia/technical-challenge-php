<?php

namespace Gonsandia\CarPoolingChallenge\Application\Service\FillCarWithQueueJourneys;

use Gonsandia\CarPoolingChallenge\Application\Service\ApplicationService;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarId;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\Journey;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyRepository;
use Psr\EventDispatcher\EventDispatcherInterface;

class FillCarWithQueueJourneysService implements ApplicationService
{
    public function __construct(
        private CarRepository            $carRepository,
        private JourneyRepository        $journeyRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function execute($request = null)
    {
        $carId = new CarId($request['car_id']);

        $car = $this->findCarOfId($carId);

        $journey = $this->findQueueJourneyOfCar($car);

        if (!is_null($journey)) {
            $car->performJourney($journey);

            $this->carRepository->save($car);
            $this->journeyRepository->save($journey);
        }

        $events = $car->getEvents();

        foreach ($events as $event) {
            $this->eventDispatcher->dispatch($event);
        }
    }

    private function findCarOfId(CarId $carId): Car
    {
        return $this->carRepository->ofId($carId);
    }

    private function findQueueJourneyOfCar(Car $car): ?Journey
    {
        return $this->journeyRepository->findPendingJourneyForCar($car);
    }
}
