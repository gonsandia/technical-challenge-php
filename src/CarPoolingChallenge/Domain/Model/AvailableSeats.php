<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Model;

use Assert\Assert;

class AvailableSeats
{
    public const MIN_SEATS = 0;
    public const MAX_SEATS = TotalSeats::MAX_SEATS;
    private int $count;

    public function __construct(int $count)
    {
        Assert::that($count)->between(self::MIN_SEATS, self::MAX_SEATS);

        $this->count = $count;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    public function __toString()
    {
        return (string)$this->getCount();
    }

    public function equals($other): bool
    {
        return $this->getCount() === $other->getCount();
    }
}
