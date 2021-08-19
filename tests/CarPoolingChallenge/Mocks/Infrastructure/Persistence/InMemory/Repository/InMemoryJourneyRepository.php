<?php

namespace Gonsandia\Tests\CarPoolingChallenge\Mocks\Infrastructure\Persistence\InMemory\Repository;

use Gonsandia\CarPoolingChallenge\Domain\Exception\JourneyNotExistsException;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\Journey;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyRepository;

class InMemoryJourneyRepository implements JourneyRepository
{
    /**
     * @var Journey[]
     */
    private $journeys = array();

    public function save(Journey $journey): void
    {
        $this->journeys[$journey->getJourneyId()->value()] = $journey;
    }

    public function remove(Journey $journey): void
    {
        unset($this->journeys[$journey->getJourneyId()->value()]);
    }

    public function findPendingJourneyForCar(Car $car): ?Journey
    {
        foreach ($this->journeys as $journey) {
            if (!$journey->hasCarAssigned() && ($car->getAvailableSeats()->value() >= $journey->getTotalPeople()->value())) {
                return $journey;
            }
        }
        return null;
    }

    public function clearTable()
    {
        $this->journeys = [];
    }

    public function ofId(JourneyId $journeyId): Journey
    {
        foreach ($this->journeys as $journey) {
            if ($journey->getJourneyId()->equals($journeyId)) {
                return $journey;
            }
        }
        throw new JourneyNotExistsException();
    }
}
