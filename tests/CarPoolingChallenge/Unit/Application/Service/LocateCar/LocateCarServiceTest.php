<?php

namespace Gonsandia\Tests\CarPoolingChallenge\Unit\Application\Service\LocateCar;

use Gonsandia\CarPoolingChallenge\Application\Service\LocateCar\LocateCarRequest;
use Gonsandia\CarPoolingChallenge\Application\Service\LocateCar\LocateCarService;
use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEventPublisher;
use Gonsandia\CarPoolingChallenge\Domain\Event\SpySubscriber;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarFound;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarId;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\Journey;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalPeople;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalSeats;
use PHPUnit\Framework\TestCase;

class LocateCarServiceTest extends TestCase
{
    private CarRepository $carRepository;

    protected function setUp(): void
    {
        // $carRepository = new InMemoryCarRepository();
        $mockCarRepository = $this->createMock(CarRepository::class);
        $this->carRepository = $mockCarRepository;
        parent::setUp();
    }
    public function testShouldPublishCarFoundEvent(): void
    {
        $subscriber = new SpySubscriber();

        $id = DomainEventPublisher::instance()->subscribe($subscriber);

        $request = new LocateCarRequest(
            new JourneyId(1)
        );

        $stubCarRepository = $this->carRepository;

        $stubCarRepository
            ->method('ofJourneyId')
            ->willReturn(
                $this->getCarOfJourney()
            );

        $service = new LocateCarService($stubCarRepository);

        $car = $service->execute($request);

        DomainEventPublisher::instance()->unsubscribe($id);

        self::assertInstanceOf(CarFound::class, $subscriber->domainEvent);
    }

    private function getCarOfJourney(): Car
    {
        return Car::from(
            new CarId(1),
            new TotalSeats(4),
            [
                Journey::from(
                    new JourneyId(1),
                    new TotalPeople(4),
                    new CarId(1)
                )
            ]
        );
    }
}
