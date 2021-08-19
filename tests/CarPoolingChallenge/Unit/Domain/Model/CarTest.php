<?php

namespace Gonsandia\Tests\CarPoolingChallenge\Unit\Domain\Model;

use Assert\InvalidArgumentException;
use Gonsandia\CarPoolingChallenge\Domain\Exception\CantDropOffException;
use Gonsandia\CarPoolingChallenge\Domain\Model\AvailableSeats;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarId;
use Gonsandia\CarPoolingChallenge\Domain\Model\Journey;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalPeople;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalSeats;
use PHPUnit\Framework\TestCase;

class CarTest extends TestCase
{
    public function testItShouldCalculateAvailableSeats(): void
    {
        $car = $this->getCarWithJourneys();
        self::assertEquals(new AvailableSeats(2), $car->getAvailableSeats());
    }

    public function testItShouldPerformJourney(): void
    {
        $car = $this->getCarWithJourneys();
        $journey = $this->getNewJourney();
        $car->performJourney($journey);

        self::assertEquals(new AvailableSeats(0), $car->getAvailableSeats());
        self::assertArrayHasKey($journey->getJourneyId()->value(), $car->getJourneys());
        self::assertEquals($car->getCarId(), $journey->getCarId());
    }

    public function testItShouldThrowInvalidArgumentExceptionOnPerformJourneyWithoutSeats(): void
    {
        $car = $this->getCarWithJourneys();
        $journey = Journey::from(
            new JourneyId(2),
            new TotalPeople(6),
        );

        $this->expectException(InvalidArgumentException::class);

        $car->performJourney($journey);
    }

    public function testItShouldDropOff(): void
    {
        $car = $this->getCarWithJourneys();
        $journey = $this->getJourney();
        $car->dropOff($journey);

        self::assertEquals(new AvailableSeats(6), $car->getAvailableSeats());
        var_dump($car->getJourneys());
        self::assertArrayNotHasKey($journey->getJourneyId()->value(), $car->getJourneys());
        self::assertNull($journey->getCarId());
    }

    public function testItShouldThrowInvalidArgumentExceptionOnDropOffForExternalJourney(): void
    {
        $car = $this->getCarWithJourneys();
        $journey = $this->getNewJourney();

        $this->expectException(CantDropOffException::class);

        $car->dropOff($journey);
    }

    private function getCarWithJourneys(): Car
    {
        $journey = $this->getJourney();
        return Car::from(
            new CarId(1),
            new TotalSeats(6),
            [
                $journey
            ]
        );
    }

    private function getJourney(): Journey
    {
        return Journey::from(
            new JourneyId(1),
            new TotalPeople(4),
            new CarId(1)
        );
    }

    private function getNewJourney(): Journey
    {
        return Journey::from(
            new JourneyId(2),
            new TotalPeople(2),
        );
    }
}
