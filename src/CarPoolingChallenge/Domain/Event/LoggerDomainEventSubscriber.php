<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Event;

use Psr\Log\LoggerInterface;

class LoggerDomainEventSubscriber implements DomainEventSubscriber
{
    private LoggerInterface $logger;

    /**
     * LoggerDomainEventSubscriber constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(DomainEvent $aDomainEvent): void
    {
        try {
            $this->logger->info(
                $aDomainEvent->jsonSerialize()
            );
        } catch (\Exception $e) {
        }
    }

    public function isSubscribedTo(DomainEvent $aDomainEvent): bool
    {
        return true;
    }
}
