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

class DropOffService implements ApplicationService
{
    private JourneyRepository $journeyRepository;

    private CarRepository $carRepository;

    public function __construct(JourneyRepository $journeyRepository, CarRepository $carRepository)
    {
        $this->journeyRepository = $journeyRepository;
        $this->carRepository = $carRepository;
    }

    public function execute($request = null): bool
    {
        $journeyId = $request->getJourneyId();

        $journey = $this->findJourneyOfId($journeyId);

        if ($this->checkIfCarAssigned($journey)) {
            $car = $this->findCarOfId($journey->getCarId());

            if (!is_null($car)) {
                $car->dropOff($journey);
                $this->carRepository->save($car);
            }
        }

        DomainEventPublisher::instance()->publish(
            DropOffDone::from($journey)
        );

        $this->journeyRepository->remove($journey);

        return true;
    }

    private function findJourneyOfId(JourneyId $journeyId): Journey
    {
        return $this->journeyRepository->ofId($journeyId);
    }

    private function findCarOfId(CarId $carId): ?Car
    {
        return $this->carRepository->ofId($carId);
    }

    private function checkIfCarAssigned(Journey $journey): bool
    {
        return !is_null($journey->getCarId());
    }
}
