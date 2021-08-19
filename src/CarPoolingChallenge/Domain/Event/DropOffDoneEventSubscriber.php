<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Event;

use Gonsandia\CarPoolingChallenge\Application\Service\FillCarWithQueueJourneys\FillCarWithQueueJourneysService;
use Gonsandia\CarPoolingChallenge\Domain\Model\DropOffDone;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DropOffDoneEventSubscriber implements DomainEventSubscriber, EventSubscriberInterface
{
    private FillCarWithQueueJourneysService $fillCarWithQueueJourneysService;

    /**
     * DropOffDoneEventSubscriber constructor.
     * @param FillCarWithQueueJourneysService $fillCarWithQueueJourneysService
     */
    public function __construct(FillCarWithQueueJourneysService $fillCarWithQueueJourneysService)
    {
        $this->fillCarWithQueueJourneysService = $fillCarWithQueueJourneysService;
    }


    public function handle(DomainEvent $aDomainEvent): void
    {
        /** @var DropOffDone $aDomainEvent */
        if (!is_null($aDomainEvent->getCarId())) {
            $request = [
                'car_id' => $aDomainEvent->getCarId()->value()
            ];

            $this->fillCarWithQueueJourneysService->execute(
                $request
            );
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            DropOffDone::class => 'handle',
        ];
    }
}
