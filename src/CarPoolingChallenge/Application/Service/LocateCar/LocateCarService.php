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
        $journeyId = $request->getJourneyId();
        $journey = $this->journeyRepository->ofId($journeyId);

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

        $events = $car->getEvents();

        foreach ($events as $event) {
            $this->eventDispatcher->dispatch($event);
        }

        return $car;
    }
}
