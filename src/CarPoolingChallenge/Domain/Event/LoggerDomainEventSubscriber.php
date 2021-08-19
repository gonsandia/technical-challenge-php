<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Event;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerDomainEventSubscriber implements DomainEventSubscriber, EventSubscriberInterface
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

    public static function getSubscribedEvents()
    {
        return [
            DomainEvent::class => 'handle',
        ];
    }
}
