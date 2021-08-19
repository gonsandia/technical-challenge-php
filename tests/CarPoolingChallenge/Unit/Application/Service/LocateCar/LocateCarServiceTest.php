<?php

namespace Gonsandia\Tests\CarPoolingChallenge\Unit\Application\Service\LocateCar;

use Gonsandia\CarPoolingChallenge\Application\Service\LocateCar\LocateCarService;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarId;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\Journey;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalPeople;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalSeats;
use Gonsandia\Tests\CarPoolingChallenge\Mocks\Infrastructure\Persistence\InMemory\Repository\InMemoryCarRepository;
use Gonsandia\Tests\CarPoolingChallenge\Mocks\Infrastructure\Persistence\InMemory\Repository\InMemoryJourneyRepository;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class LocateCarServiceTest extends TestCase
{
    private CarRepository $carRepository;

    private JourneyRepository $journeyRepository;

    private EventDispatcherInterface $eventDispatcher;

    protected function setUp(): void
    {
        $this->setupCarRepository();
        $this->setupJourneyRepository();
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        parent::setUp();
    }

    /**
     * @test
     */
    public function ItShouldReturnNull(): void
    {

        $request = [
            'journey_id' => 2
        ];

        $service = new LocateCarService($this->carRepository, $this->journeyRepository, $this->eventDispatcher);

        $car = $service->execute($request);

        $dummyCar = null;

        $this->assertEquals($dummyCar, $car);
    }

    /**
     * @test
     */
    public function ItShouldFindTheCar(): void
    {
        $request = [
            'journey_id' => 1
        ];

        $service = new LocateCarService($this->carRepository, $this->journeyRepository, $this->eventDispatcher);

        $car = $service->execute($request);

        $this->assertEquals( new CarId(1), $car->getCarId());
        $this->assertEquals( new TotalSeats(5), $car->getTotalSeats());
    }

    private function setupCarRepository()
    {
        $carRepository = new InMemoryCarRepository();
        $this->carRepository = $carRepository;

        $journey1 = Journey::from(
            new JourneyId(1),
            new TotalPeople(3),
            new CarId(1)
        );

        $car = Car::from(
            new CarId(1),
            new TotalSeats(5)
        );

        $car
            ->performJourney($journey1);

        $cars = [
            $car
        ];

        $this->carRepository->loadCars($cars);
    }

    private function setupJourneyRepository()
    {
        $journeyRepository = new InMemoryJourneyRepository();
        $this->journeyRepository = $journeyRepository;

        $journey1 = Journey::from(
            new JourneyId(1),
            new TotalPeople(3),
            new CarId(1)
        );

        $journey2 = Journey::from(
            new JourneyId(2),
            new TotalPeople(3)
        );

        $this->journeyRepository->save($journey1);
        $this->journeyRepository->save($journey2);
    }
}
