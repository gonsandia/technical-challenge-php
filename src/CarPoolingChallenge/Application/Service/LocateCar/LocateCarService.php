<?php

namespace Gonsandia\CarPoolingChallenge\Application\Service\LocateCar;

use Gonsandia\CarPoolingChallenge\Application\Service\ApplicationService;
use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEventPublisher;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarFound;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;

class LocateCarService implements ApplicationService
{
    public function __construct(
        private CarRepository     $carRepository,
        private JourneyRepository $journeyRepository,
        private EventDispatcher   $eventDispatcher
    ) {
    }

    public function execute($request = null): ?Car
    {
        $journeyId = new JourneyId($request['journey_id']);

        $journey = $this->journeyRepository->ofId($journeyId);

        $car = $this->carRepository->ofJourneyId($journey->getJourneyId());

        if (!is_null($car)) {
            DomainEventPublisher::instance()->publish(
                CarFound::from($journeyId, $car)
            );
        }

        $events = $car->getEvents();

        foreach ($events as $event) {
            $this->eventDispatcher->dispatch($event);
        }

        return $car;
    }
}
