<?php

namespace Gonsandia\Tests\CarPoolingChallenge\Unit\Application\Service\DropOff;

use Gonsandia\CarPoolingChallenge\Application\Service\DropOff\DropOffService;
use Gonsandia\CarPoolingChallenge\Domain\Exception\CarNotExistsException;
use Gonsandia\CarPoolingChallenge\Domain\Exception\JourneyNotExistsException;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarId;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyRepository;
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

        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        parent::setUp();
    }

    /**
     * @test
     */
    public function ItShouldDoTheDropOffFromRequest(): void
    {
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
            'journey_id' => 1
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
        $request = [
            'journey_id' => 1
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
            'journey_id' => 1
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

        $cars = [
            Car::from(
                new CarId(1),
                new TotalSeats(6)
            )
        ];

        $this->carRepository->loadCars($cars);
    }

    private function setupJourneyRepository()
    {
        $journeyRepository = new InMemoryJourneyRepository();
        $this->journeyRepository = $journeyRepository;
    }
}
