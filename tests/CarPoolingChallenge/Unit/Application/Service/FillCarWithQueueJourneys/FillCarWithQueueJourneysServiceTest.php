<?php

namespace Gonsandia\Tests\CarPoolingChallenge\Unit\Application\Service\FillCarWithQueueJourneys;

use Gonsandia\CarPoolingChallenge\Application\Service\FillCarWithQueueJourneys\FillCarWithQueueJourneysRequest;
use Gonsandia\CarPoolingChallenge\Application\Service\FillCarWithQueueJourneys\FillCarWithQueueJourneysService;
use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEventPublisher;
use Gonsandia\CarPoolingChallenge\Domain\Event\SpySubscriber;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarId;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\Journey;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyPerformed;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalPeople;
use PHPUnit\Framework\TestCase;

class FillCarWithQueueJourneysServiceTest extends TestCase
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

        $request = new FillCarWithQueueJourneysRequest(
            new CarId('1')
        );

        $stubJourneyRepository = $this->journeyRepository;

        $stubJourneyRepository
            ->method('findPendingJourneyForCar')
            ->willReturn(
                Journey::from(
                    new JourneyId(1),
                    new TotalPeople(4)
                )
            );

        $service = new FillCarWithQueueJourneysService($this->carRepository, $stubJourneyRepository);

        $service->execute($request);

        DomainEventPublisher::instance()->unsubscribe($id);

        self::assertInstanceOf(JourneyPerformed::class, $subscriber->domainEvent);
    }
}
