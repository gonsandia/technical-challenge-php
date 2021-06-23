<?php

namespace Gonsandia\CarPoolingChallenge\Application\Service\PerformJourney;

use Gonsandia\CarPoolingChallenge\Application\Service\ApplicationService;
use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEventPublisher;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\Journey;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyPerformed;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyQueued;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyRepository;

class PerformJourneyService implements ApplicationService
{
    private JourneyRepository $journeyRepository;

    private CarRepository $carRepository;

    public function __construct(JourneyRepository $journeyRepository, CarRepository $carRepository)
    {
        $this->journeyRepository = $journeyRepository;
        $this->carRepository = $carRepository;
    }


    public function execute($request = null)
    {
        $journey = Journey::from($request->getId(), $request->getPeople(), null);

        $car = $this->findCarForJourney($journey);

        if (!is_null($car)) {
            $car->performJourney($journey);
            $this->carRepository->save($car);
        }

        $this->journeyRepository->save($journey);

        if (!is_null($journey->getCarId())) {
            DomainEventPublisher::instance()->publish(
                JourneyPerformed::from($journey)
            );
        } else {
            DomainEventPublisher::instance()->publish(
                JourneyQueued::from($journey)
            );
        }

        return $journey;
    }

    private function findCarForJourney(Journey $journey): ?Car
    {
        $car = $this->carRepository->findCarWithEnoughEmptySeatsForGroup($journey->getTotalPeople());

        return $car;
    }
}
