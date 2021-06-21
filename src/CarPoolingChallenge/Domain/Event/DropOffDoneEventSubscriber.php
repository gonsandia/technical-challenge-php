<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Event;

use Gonsandia\CarPoolingChallenge\Application\Service\FillCarWithQueueJourneys\FillCarWithQueueJourneysRequest;
use Gonsandia\CarPoolingChallenge\Application\Service\FillCarWithQueueJourneys\FillCarWithQueueJourneysService;
use Gonsandia\CarPoolingChallenge\Domain\Model\DropOffDone;

class DropOffDoneEventSubscriber implements DomainEventSubscriber
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


    public function handle($aDomainEvent): void
    {
        $request = new FillCarWithQueueJourneysRequest(
            $aDomainEvent->carId()
        );

        $this->fillCarWithQueueJourneysService->execute(
            $request
        );
    }

    public function isSubscribedTo(DomainEvent $aDomainEvent): bool
    {
        return $aDomainEvent instanceof DropOffDone;
    }
}
