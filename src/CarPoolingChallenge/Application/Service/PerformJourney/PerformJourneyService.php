<?php

namespace Gonsandia\CarPoolingChallenge\Application\Service\PerformJourney;

use Gonsandia\CarPoolingChallenge\Application\Service\ApplicationService;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\Journey;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyQueued;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalPeople;
use Psr\EventDispatcher\EventDispatcherInterface;

class PerformJourneyService implements ApplicationService
{
    public function __construct(
        private JourneyRepository        $journeyRepository,
        private CarRepository            $carRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }


    public function execute($request = null)
    {
        $events = [];
        $journey = Journey::from(
            new JourneyId($request['id']),
            new TotalPeople($request['people'])
        );

        $car = $this->carRepository->findCarForPeople($journey->getTotalPeople());

        if (!is_null($car)) {
            $car->performJourney($journey);
            $this->carRepository->save($car);
        } else {
            $events[] = JourneyQueued::from($journey);
        }

        $this->journeyRepository->save($journey);

        $events = array_merge($events, $car->getEvents());

        foreach ($events as $event) {
            $this->eventDispatcher->dispatch($event);
        }

        return $journey;
    }
}
