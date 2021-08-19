<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Model;

use Assert\Assert;

class AvailableSeats implements \Stringable
{
    public const MIN_SEATS = 0;
    public const MAX_SEATS = TotalSeats::MAX_SEATS;

    private int $value;

    public function __construct(int $value)
    {
        Assert::that($value)->between(self::MIN_SEATS, self::MAX_SEATS);
        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals($other): bool
    {
        return $this->value() === $other->value();
    }

    public function __toString(): string
    {
        return (string)$this->value();
    }
}
