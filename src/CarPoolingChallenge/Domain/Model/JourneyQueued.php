<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Model;

use DateTimeInterface;
use Gonsandia\CarPoolingChallenge\Domain\Event\DomainEvent;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class JourneyQueued implements DomainEvent
{
    private JourneyId $journeyId;

    private DateTimeInterface $occurredOn;

    public function __construct(JourneyId $journeyId, DateTimeInterface $occurredOn)
    {
        $this->journeyId = $journeyId;
        $this->occurredOn = $occurredOn;
    }


    public static function from(Journey $journey): self
    {
        return new self($journey->getJourneyId(), new \DateTimeImmutable());
    }

    public function occurredOn(): DateTimeInterface
    {
        return $this->occurredOn;
    }

    public function jsonSerialize()
    {
        return json_encode([
            'id' => $this->id(),
            'journey_id' => $this->journeyId,
            'occurred_on' => $this->occurredOn->format(DATE_ATOM),
            'type' => __CLASS__
        ], JSON_THROW_ON_ERROR);
    }

    public function id(): UuidInterface
    {
        return Uuid::uuid4();
    }
}
