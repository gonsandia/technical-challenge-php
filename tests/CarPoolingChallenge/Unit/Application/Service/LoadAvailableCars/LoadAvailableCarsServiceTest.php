<?php

namespace Gonsandia\Tests\CarPoolingChallenge\Unit\Application\Service\LoadAvailableCars;

use Assert\InvalidArgumentException;
use Gonsandia\CarPoolingChallenge\Application\Service\LoadAvailableCars\LoadAvailableCarsRequest;
use Gonsandia\CarPoolingChallenge\Application\Service\LoadAvailableCars\LoadAvailableCarsService;
use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEventPublisher;
use Gonsandia\CarPoolingChallenge\Domain\Event\SpySubscriber;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarsLoaded;
use PHPUnit\Framework\TestCase;

class LoadAvailableCarsServiceTest extends TestCase
{
    private CarRepository $carRepository;

    protected function setUp(): void
    {
        $mockCarRepository = $this->createMock(CarRepository::class);
        $this->carRepository = $mockCarRepository;
        parent::setUp();
    }

    public function testShouldPublishCarsLoadedEvent(): void
    {
        $subscriber = new SpySubscriber();

        $id = DomainEventPublisher::instance()->subscribe($subscriber);

        $request = new LoadAvailableCarsRequest(
            [
                ['id' => 1,
                    'seats' => 4],
                ['id' => 2,
                    'seats' => 5],
                ['id' => 3,
                    'seats' => 5],
                ['id' => 4,
                    'seats' => 6],
                ['id' => 5,
                    'seats' => 6]
            ]
        );

        $service = new LoadAvailableCarsService($this->carRepository);

        $service->execute($request);

        DomainEventPublisher::instance()->unsubscribe($id);

        self::assertInstanceOf(CarsLoaded::class, $subscriber->domainEvent);
    }

    public function testShouldCreateAvailableCarListFromRequest(): void
    {
        $request = new LoadAvailableCarsRequest(
            [
                ['id' => 1,
                    'seats' => 4],
                ['id' => 2,
                    'seats' => 5],
                ['id' => 3,
                    'seats' => 5],
                ['id' => 4,
                    'seats' => 6],
                ['id' => 5,
                    'seats' => 6]
            ]
        );

        $service = new LoadAvailableCarsService($this->carRepository);

        $result = $service->execute($request);

        self::assertNotEmpty($result->getCars());
    }

    public function testShouldNotCreateAvailableCarListFromRequestInvalidNumberOfSeats(): void
    {
        $request = new LoadAvailableCarsRequest(
            [
                ['id' => 1,
                    'seats' => 0],
                ['id' => 1,
                    'seats' => 3],
                ['id' => 2,
                    'seats' => 5],
                ['id' => 3,
                    'seats' => 5],
                ['id' => 4,
                    'seats' => 7],
                ['id' => 5,
                    'seats' => 126]
            ]
        );

        $service = new LoadAvailableCarsService($this->carRepository);

        $this->expectException(InvalidArgumentException::class);

        $service->execute($request);
    }

    public function testShouldNotCreateAvailableCarListFromRequestInvalidIds(): void
    {
        $request = new LoadAvailableCarsRequest(
            [
                ['id' => 0,
                    'seats' => 4],
                ['id' => 2,
                    'seats' => 5],
                ['id' => 3,
                    'seats' => 5],
                ['id' => 4,
                    'seats' => 6],
                ['id' => -5,
                    'seats' => 6]
            ]
        );

        $service = new LoadAvailableCarsService($this->carRepository);

        $this->expectException(InvalidArgumentException::class);

        $service->execute($request);
    }
}
