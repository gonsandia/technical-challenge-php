<?php

namespace Gonsandia\Tests\CarPoolingChallenge\Unit\Application\Service\PerformJourney;

use Gonsandia\CarPoolingChallenge\Application\Service\PerformJourney\PerformJourneyRequest;
use Gonsandia\CarPoolingChallenge\Application\Service\PerformJourney\PerformJourneyService;
use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEventPublisher;
use Gonsandia\CarPoolingChallenge\Domain\Event\SpySubscriber;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarId;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyPerformed;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyQueued;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalPeople;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalSeats;
use PHPUnit\Framework\TestCase;

class PerformJourneyServiceTest extends TestCase
{
    private CarRepository $carRepository;

    private JourneyRepository $journeyRepository;

    protected function setUp(): void
    {
        // $carRepository = new InMemoryCarRepository();
        $mockCarRepository = $this->createMock(CarRepository::class);
        $mockJourneyRepository = $this->createMock(JourneyRepository::class);
        $this->carRepository = $mockCarRepository;
        $this->journeyRepository = $mockJourneyRepository;
        parent::setUp();
    }

    public function testShouldPublishJourneyPerformedEvent(): void
    {
        $subscriber = new SpySubscriber();

        $id = DomainEventPublisher::instance()->subscribe($subscriber);

        $request = new PerformJourneyRequest(
            new JourneyId(1),
            new TotalPeople(4)
        );

        $stubCarRepository = $this->carRepository;

        $stubCarRepository
            ->method('findCarForPeople')
            ->willReturn(
                $this->getEmptyCar()
            );

        $service = new PerformJourneyService($this->journeyRepository, $stubCarRepository);

        $service->execute($request);

        DomainEventPublisher::instance()->unsubscribe($id);

        self::assertInstanceOf(JourneyPerformed::class, $subscriber->domainEvent);
    }

    public function testShouldPublishJourneyQueuedEvent(): void
    {
        $subscriber = new SpySubscriber();

        $id = DomainEventPublisher::instance()->subscribe($subscriber);

        $request = new PerformJourneyRequest(
            new JourneyId(1),
            new TotalPeople(5)
        );

        $service = new PerformJourneyService($this->journeyRepository, $this->carRepository);

        $service->execute($request);

        DomainEventPublisher::instance()->unsubscribe($id);

        self::assertInstanceOf(JourneyQueued::class, $subscriber->domainEvent);
    }

    private function getEmptyCar(): Car
    {
        return Car::from(
            new CarId(1),
            new TotalSeats(6),
            []
        );
    }
}
