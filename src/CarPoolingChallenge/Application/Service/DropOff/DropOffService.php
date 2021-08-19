<?php

namespace Gonsandia\CarPoolingChallenge\Application\Service\DropOff;

use Gonsandia\CarPoolingChallenge\Application\Service\ApplicationService;
use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEventPublisher;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarId;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\DropOffDone;
use Gonsandia\CarPoolingChallenge\Domain\Model\Journey;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;

class DropOffService implements ApplicationService
{
    public function __construct(
        private JourneyRepository $journeyRepository,
        private CarRepository $carRepository,
        private EventDispatcher   $eventDispatcher
    )
    {
    }

    public function execute($request = null): bool
    {
        $journeyId = new JourneyId($request['journey_id']);

        $journey = $this->journeyRepository->ofId($journeyId);

        if ($journey->hasCarAssigned()) {
            $car = $this->carRepository->ofId($journey->getCarId());
            $car->dropOff($journey);
            $this->carRepository->save($car);
        }

        $this->journeyRepository->remove($journey);

        DomainEventPublisher::instance()->publish(
            DropOffDone::from($journey)
        );

        $events = $car->getEvents();

        foreach ($events as $event) {
            $this->eventDispatcher->dispatch($event);
        }

        return true;
    }
}
