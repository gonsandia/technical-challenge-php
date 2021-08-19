<?php

namespace Gonsandia\CarPoolingChallenge\Application\Service\PerformJourney;

use Gonsandia\CarPoolingChallenge\Application\Service\ApplicationService;
use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEventPublisher;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\Journey;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyPerformed;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyQueued;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalPeople;
use Symfony\Component\EventDispatcher\EventDispatcher;

class PerformJourneyService implements ApplicationService
{
    public function __construct(
        private JourneyRepository $journeyRepository,
        private CarRepository $carRepository,
        private EventDispatcher $eventDispatcher
    ) {
    }


    public function execute($request = null)
    {
        $journey = Journey::from(
            new JourneyId($request['id']),
            new TotalPeople($request['people'])
        );

        $car = $this->carRepository->findCarForPeople($journey->getTotalPeople());

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

        $events = $car->getEvents();

        foreach ($events as $event) {
            $this->eventDispatcher->dispatch($event);
        }

        return $journey;
    }
}
