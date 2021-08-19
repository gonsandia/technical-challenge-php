<?php

namespace Gonsandia\Tests\CarPoolingChallenge\Unit\Application\Service\FillCarWithQueueJourneys;

use Gonsandia\CarPoolingChallenge\Application\Service\FillCarWithQueueJourneys\FillCarWithQueueJourneysService;
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

class FillCarWithQueueJourneysServiceTest extends TestCase
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
    public function ItShouldPerformTheJourney(): void
    {
        $journey1 = Journey::from(
            new JourneyId(1),
            new TotalPeople(2),
            new CarId(1)
        );

        $car = Car::from(
            new CarId(1),
            new TotalSeats(6)
        );

        $car
            ->performJourney($journey1);

        $cars = [
            $car
        ];

        $this->carRepository->loadCars($cars);

        $request = [
            'car_id' => 1,
        ];

        $service = new FillCarWithQueueJourneysService($this->carRepository, $this->journeyRepository, $this->eventDispatcher);

        $service->execute($request);

        $journey = $this->journeyRepository->ofId(new JourneyId(2));

        $this->assertTrue($journey->hasCarAssigned());
    }

    /**
     * @test
     */
    public function ItShouldQueueTheJourney(): void
    {
        $journey1 = Journey::from(
            new JourneyId(1),
            new TotalPeople(2),
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

        $request = [
            'car_id' => 1,
        ];

        $service = new FillCarWithQueueJourneysService($this->carRepository, $this->journeyRepository, $this->eventDispatcher);

        $service->execute($request);

        $journey = $this->journeyRepository->ofId(new JourneyId(2));

        $this->assertFalse($journey->hasCarAssigned());
    }

    private function setupCarRepository()
    {
        $carRepository = new InMemoryCarRepository();
        $this->carRepository = $carRepository;
    }

    private function setupJourneyRepository()
    {
        $journeyRepository = new InMemoryJourneyRepository();
        $this->journeyRepository = $journeyRepository;

        $journey1 = Journey::from(
            new JourneyId(1),
            new TotalPeople(2),
            new CarId(1)
        );

        $journey2 = Journey::from(
            new JourneyId(2),
            new TotalPeople(4)
        );

        $this->journeyRepository->save($journey1);
        $this->journeyRepository->save($journey2);
    }
}
