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

        if ($journey->hasCarAssigned()) {
            $car = $this->findCarOfId($journey->getCarId());
            $car->dropOff($journey);
            $this->carRepository->save($car);
        }

        $this->journeyRepository->remove($journey);

        DomainEventPublisher::instance()->publish(
            DropOffDone::from($journey)
        );

        return true;
    }

    private function findJourneyOfId(JourneyId $journeyId): Journey
    {
        return $this->journeyRepository->ofId($journeyId);
    }

    private function findCarOfId(CarId $carId): Car
    {
        return $this->carRepository->ofId($carId);
    }
}
