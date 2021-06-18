<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Event;

class SpySubscriber implements DomainEventSubscriber
{
    public ?DomainEvent $domainEvent = null;

    public function handle(DomainEvent $aDomainEvent): void
    {
        $this->domainEvent = $aDomainEvent;
    }

    public function isSubscribedTo(DomainEvent $aDomainEvent): bool
    {
        return true;
    }
}
