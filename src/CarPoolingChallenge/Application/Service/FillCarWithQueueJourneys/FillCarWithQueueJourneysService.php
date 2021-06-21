<?php

namespace Gonsandia\CarPoolingChallenge\Application\Service\FillCarWithQueueJourneys;

use Gonsandia\CarPoolingChallenge\Application\Service\ApplicationService;
use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEventPublisher;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarId;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\Journey;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyPerformed;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyRepository;

class FillCarWithQueueJourneysService implements ApplicationService
{
    private CarRepository $carRepository;

    private JourneyRepository $journeyRepository;

    public function __construct(CarRepository $carRepository, JourneyRepository $journeyRepository)
    {
        $this->carRepository = $carRepository;
        $this->journeyRepository = $journeyRepository;
    }


    public function execute($request = null)
    {
        $car = $this->findCarOfId($request->getCarId());

        $journey = $this->findQueueJourneyOfCar($car);

        if (!is_null($journey)) {
            $car->performJourney($journey);

            DomainEventPublisher::instance()->publish(
                JourneyPerformed::from($journey)
            );

            $this->carRepository->save($car);
            $this->journeyRepository->save($journey);
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