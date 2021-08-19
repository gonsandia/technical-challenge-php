<?php

namespace Gonsandia\Tests\CarPoolingChallenge\Unit\Application\Service\DropOff;

use Gonsandia\CarPoolingChallenge\Application\Service\DropOff\DropOffService;
use Gonsandia\CarPoolingChallenge\Domain\Exception\CarNotExistsException;
use Gonsandia\CarPoolingChallenge\Domain\Exception\JourneyNotExistsException;
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

class DropOffServiceTest extends TestCase
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
    public function ItShouldDoTheDropOffFromRequest(): void
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
        $this->journeyRepository->save($journey1);

        $request = [
            'journey_id' => 1
        ];

        $service = new DropOffService($this->journeyRepository, $this->carRepository, $this->eventDispatcher);

        $result = $service->execute($request);

        self::assertTrue($result);
    }

    /**
     * @test
     */
    public function ItShouldThrowJourneyNotFoundExceptionTheDropOffFromRequest(): void
    {
        $request = [
            'journey_id' => 3
        ];

        $service = new DropOffService($this->journeyRepository, $this->carRepository, $this->eventDispatcher);

        $this->expectException(JourneyNotExistsException::class);

        $service->execute($request);
    }

    /**
     * @test
     */
    public function ItShouldThrowCarNotExistsExceptionTheDropOffFromRequest(): void
    {
        $journey2 = Journey::from(
            new JourneyId(4),
            new TotalPeople(4),
            new CarId(2)
        );

        $this->journeyRepository->save($journey2);

        $request = [
            'journey_id' => 4
        ];

        $service = new DropOffService($this->journeyRepository, $this->carRepository, $this->eventDispatcher);

        $this->expectException(CarNotExistsException::class);

        $service->execute($request);
    }

    /**
     * @test
     */
    public function ItShouldDoDropOffWithJourneyWithoutCarAssigned(): void
    {

        $request = [
            'journey_id' => 2
        ];

        $service = new DropOffService($this->journeyRepository, $this->carRepository, $this->eventDispatcher);

        $result = $service->execute($request);

        self::assertTrue($result);
    }

    /**
     * @test
     */
    public function ItShouldDoDropOffWithJourneyWithCarAssigned(): void
    {
        $request = [
            'journey_id' => 1
        ];

        $service = new DropOffService($this->journeyRepository, $this->carRepository, $this->eventDispatcher);

        $result = $service->execute($request);

        self::assertTrue($result);
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
