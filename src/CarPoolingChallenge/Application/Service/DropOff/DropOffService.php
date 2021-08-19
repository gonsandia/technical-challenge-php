<?php

namespace Gonsandia\CarPoolingChallenge\Application\Service\DropOff;

use Gonsandia\CarPoolingChallenge\Application\Service\ApplicationService;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyRepository;
use Psr\EventDispatcher\EventDispatcherInterface;

class DropOffService implements ApplicationService
{
    public function __construct(
        private JourneyRepository        $journeyRepository,
        private CarRepository            $carRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function execute($request = null): bool
    {
        $journeyId = new JourneyId($request['journey_id']);

        $journey = $this->journeyRepository->ofId($journeyId);

        if ($journey->hasCarAssigned()) {
            $car = $this->carRepository->ofId($journey->getCarId());
            $car->dropOff($journey);
            $this->carRepository->save($car);

            $events = $car->getEvents();

            foreach ($events as $event) {
                $this->eventDispatcher->dispatch($event);
            }
        }

        $this->journeyRepository->remove($journey);


        return true;
    }
}
