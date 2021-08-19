<?php

namespace Gonsandia\Tests\CarPoolingChallenge\Unit\Application\Service\LoadAvailableCars;

use Assert\InvalidArgumentException;
use Gonsandia\CarPoolingChallenge\Application\Service\LoadAvailableCars\LoadAvailableCarsService;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyRepository;
use Gonsandia\Tests\CarPoolingChallenge\Mocks\Infrastructure\Persistence\InMemory\Repository\InMemoryCarRepository;
use Gonsandia\Tests\CarPoolingChallenge\Mocks\Infrastructure\Persistence\InMemory\Repository\InMemoryJourneyRepository;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class LoadAvailableCarsServiceTest extends TestCase
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
    public function ItShouldCreateAvailableCarListFromRequest(): void
    {
        $request = [
            'cars' => [
                ['id' => 1, 'seats' => 4],
                ['id' => 2, 'seats' => 5],
                ['id' => 3, 'seats' => 5],
                ['id' => 4, 'seats' => 5],
                ['id' => 5, 'seats' => 6],
            ]
        ];

        $service = new LoadAvailableCarsService($this->carRepository, $this->eventDispatcher);


        $cars = $service->execute($request);

        self::assertNotEmpty($cars);
    }

    /**
     * @test
     */
    public function ItShouldNotCreateAvailableCarListFromRequestInvalidNumberOfSeats(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $request = [
            'cars' => [
                ['id' => 1, 'seats' => 0],
                ['id' => 2, 'seats' => 5],
                ['id' => 3, 'seats' => 5],
                ['id' => 4, 'seats' => 5],
                ['id' => 5, 'seats' => 126],
            ]
        ];

        $service = new LoadAvailableCarsService($this->carRepository, $this->eventDispatcher);

        $service->execute($request);
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
    }
}
