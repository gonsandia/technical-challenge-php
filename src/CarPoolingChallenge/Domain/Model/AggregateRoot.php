<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Model;

use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEvent;

interface AggregateRoot
{
    public function trigger(DomainEvent $event): void;

    public function getEvents(): array;
}
