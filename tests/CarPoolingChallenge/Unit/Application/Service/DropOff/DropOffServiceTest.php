<?php

namespace Gonsandia\Tests\CarPoolingChallenge\Unit\Application\Service\DropOff;

use Gonsandia\CarPoolingChallenge\Application\Service\DropOff\DropOffRequest;
use Gonsandia\CarPoolingChallenge\Application\Service\DropOff\DropOffService;
use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEventPublisher;
use Gonsandia\CarPoolingChallenge\Domain\Event\SpySubscriber;
use Gonsandia\CarPoolingChallenge\Domain\Exception\CarNotExistsException;
use Gonsandia\CarPoolingChallenge\Domain\Exception\JourneyNotExistsException;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarId;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\DropOffDone;
use Gonsandia\CarPoolingChallenge\Domain\Model\Journey;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalPeople;
use PHPUnit\Framework\TestCase;

class DropOffServiceTest extends TestCase
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

    public function testShouldPublishDropOffDoneEvent(): void
    {
        $subscriber = new SpySubscriber();

        $id = DomainEventPublisher::instance()->subscribe($subscriber);

        $request = new DropOffRequest(
            new JourneyId(1)
        );

        $service = new DropOffService($this->journeyRepository, $this->carRepository);

        $service->execute($request);

        DomainEventPublisher::instance()->unsubscribe($id);

        self::assertInstanceOf(DropOffDone::class, $subscriber->domainEvent);
    }

    public function testShouldDoTheDropOffFromRequest(): void
    {
        $request = new DropOffRequest(
            new JourneyId(1)
        );

        $service = new DropOffService($this->journeyRepository, $this->carRepository);

        $service->execute($request);

        $result = $service->execute($request);

        self::assertTrue($result);
    }

    public function testShouldThrowJourneyNotFoundExceptionTheDropOffFromRequest(): void
    {
        $request = new DropOffRequest(
            new JourneyId(1)
        );

        $stubJourneyRepository = $this->journeyRepository;

        $stubJourneyRepository
            ->method('ofId')
            ->willThrowException(new JourneyNotExistsException());

        $service = new DropOffService($stubJourneyRepository, $this->carRepository);

        $this->expectException(JourneyNotExistsException::class);

        $service->execute($request);
    }

    public function testShouldThrowCarNotExistsExceptionTheDropOffFromRequest(): void
    {
        $request = new DropOffRequest(
            new JourneyId(1)
        );

        $stubJourneyRepository = $this->journeyRepository;
        $stubJourneyRepository
            ->method('ofId')
            ->willReturn(
                Journey::from(
                    new JourneyId(1),
                    new TotalPeople(4),
                    new CarId(1)
                )
            );

        $stubCarRepository = $this->carRepository;
        $stubCarRepository
            ->method('ofId')
            ->willThrowException(new CarNotExistsException());

        $service = new DropOffService($stubJourneyRepository, $stubCarRepository);

        $this->expectException(CarNotExistsException::class);

        $service->execute($request);
    }

    public function testShouldDoDropOffWithJourneyWithoutCarAssigned(): void
    {
        $request = new DropOffRequest(
            new JourneyId(1)
        );

        $stubJourneyRepository = $this->journeyRepository;

        $stubJourneyRepository
            ->method('ofId')
            ->willReturn(
                Journey::from(
                    new JourneyId(1),
                    new TotalPeople(4),
                    null
                )
            );

        $service = new DropOffService($stubJourneyRepository, $this->carRepository);

        $result = $service->execute($request);

        self::assertTrue($result);
    }

    public function testShouldDoDropOffWithJourneyWithCarAssigned(): void
    {
        $request = new DropOffRequest(
            new JourneyId(1)
        );

        $stubJourneyRepository = $this->journeyRepository;

        $stubJourneyRepository
            ->method('ofId')
            ->willReturn(
                Journey::from(
                    new JourneyId(1),
                    new TotalPeople(4),
                    new CarId(1)
                )
            );

        $service = new DropOffService($stubJourneyRepository, $this->carRepository);

        $result = $service->execute($request);

        self::assertTrue($result);
    }
}
