<?php

namespace Gonsandia\Tests\CarPoolingChallenge\Unit\Application\Service\PerformJourney;

use Gonsandia\CarPoolingChallenge\Application\Service\PerformJourney\PerformJourneyService;
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

class PerformJourneyServiceTest extends TestCase
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

        $request = [
            'id' => 1,
            'people' => 1
        ];

        $service = new PerformJourneyService($this->journeyRepository, $this->carRepository, $this->eventDispatcher);

        $journey = $service->execute($request);

        $this->assertTrue($journey->hasCarAssigned());
    }

    /**
     * @test
     */
    public function ItShouldQueueTheJourney(): void
    {

        $request = [
            'id' => 1,
            'people' => 5
        ];

        $service = new PerformJourneyService($this->journeyRepository, $this->carRepository,$this->eventDispatcher);

        $journey = $service->execute($request);

        $this->assertFalse($journey->hasCarAssigned());
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

        $this->journeyRepository->save($journey1);
    }
}
