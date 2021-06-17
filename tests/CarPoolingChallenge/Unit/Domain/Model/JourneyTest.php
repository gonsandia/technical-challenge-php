<?php

namespace Gonsandia\Tests\CarPoolingChallenge\Unit\Domain\Model;

use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarId;
use Gonsandia\CarPoolingChallenge\Domain\Model\Journey;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalPeople;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalSeats;
use PHPUnit\Framework\TestCase;

class JourneyTest extends TestCase
{

    public function testItShouldAssignCarId(): void
    {
        $car = $this->getCarWithJourneys();
        $journey = $this->getJourney();
        $journey->assignCarId($car->getCarId());

        self::assertEquals($car->getCarId(), $journey->getCarId());
    }

    private function getCarWithJourneys(): Car
    {
        return Car::from(
            new CarId(1),
            new TotalSeats(5),
            [
                Journey::from(
                    new JourneyId(1),
                    new TotalPeople(5)
                )
            ]
        );
    }

    private function getJourney(): Journey
    {
        return Journey::from(
            new JourneyId(1),
            new TotalPeople(4),
        );
    }
}
