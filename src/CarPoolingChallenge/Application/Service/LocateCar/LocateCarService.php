<?php

namespace Gonsandia\CarPoolingChallenge\Application\Service\LocateCar;

use Gonsandia\CarPoolingChallenge\Application\Service\ApplicationService;
use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEventPublisher;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarFound;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyRepository;
use Psr\EventDispatcher\EventDispatcherInterface;

class LocateCarService implements ApplicationService
{
    public function __construct(
        private CarRepository            $carRepository,
        private JourneyRepository        $journeyRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function execute($request = null): ?Car
    {
        $journeyId = new JourneyId($request['journey_id']);

        $journey = $this->journeyRepository->ofId($journeyId);

        $car = $this->carRepository->ofJourneyId($journey->getJourneyId());

        if (!is_null($car)) {
            $event = CarFound::from($journeyId, $car);
            $this->eventDispatcher->dispatch($event);
        }

        return $car;
    }
}
