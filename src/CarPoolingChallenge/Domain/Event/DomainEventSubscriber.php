<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Event;

interface DomainEventSubscriber
{
    public function handle(DomainEvent $aDomainEvent);
}
