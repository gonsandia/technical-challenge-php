<?php

declare(strict_types=1);

namespace Gonsandia\CarPoolingChallenge\Domain\Event;

trait TriggerEventsTrait
{
    private array $events = [];

    public function trigger(DomainEvent $event): void
    {
        $this->events[] = $event;
    }

    public function getEvents(): array
    {
        return $this->events;
    }
}
