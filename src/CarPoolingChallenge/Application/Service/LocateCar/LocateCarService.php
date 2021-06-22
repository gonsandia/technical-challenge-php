<?php

namespace Gonsandia\CarPoolingChallenge\Application\Service\LocateCar;

use Gonsandia\CarPoolingChallenge\Application\Service\ApplicationService;
use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEventPublisher;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarFound;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyRepository;

class LocateCarService implements ApplicationService
{
    private CarRepository $carRepository;

    private JourneyRepository $journeyRepository;

    public function __construct(CarRepository $carRepository, JourneyRepository $journeyRepository)
    {
        $this->carRepository = $carRepository;
        $this->journeyRepository = $journeyRepository;
    }

    public function execute($request = null): ?Car
    {
        $journeyId = $request->getJourneyId();
        $journey =  $this->journeyRepository->ofId($journeyId);

        return $this->locateCarOfJourneyId($journey->getJourneyId());
    }


    private function locateCarOfJourneyId(JourneyId $journeyId): ?Car
    {
        $car = $this->carRepository->ofJourneyId($journeyId);

        if (!is_null($car)) {
            DomainEventPublisher::instance()->publish(
                CarFound::from($journeyId, $car)
            );
        }

        return $car;
    }
}
