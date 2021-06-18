<?php

namespace Gonsandia\CarPoolingChallenge\Application\Service\LocateCar;

use Gonsandia\CarPoolingChallenge\Application\Service\ApplicationService;
use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEventPublisher;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarFound;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;

class LocateCarService implements ApplicationService
{
    private CarRepository $carRepository;

    public function __construct(CarRepository $carRepository)
    {
        $this->carRepository = $carRepository;
    }


    public function execute($request = null): ?Car
    {
        $journeyId = $request->getJourneyId();
        return $this->locateCarOfJourneyId($journeyId);
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
