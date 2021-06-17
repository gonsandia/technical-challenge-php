<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Model;

use Assert\Assert;

class CarId
{
    public const FIRST_ID = 1;
    private int $id;

    /**
     * TotalSeats constructor.
     * @param int $count
     */
    public function __construct(int $count)
    {
        Assert::that($count)->between(self::FIRST_ID, PHP_INT_MAX);

        $this->id = $count;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function __toString()
    {
        return (string)$this->getId();
    }

    public function equals($other): bool
    {
        return $this->getId() === $other->getId();
    }

    public function addJourney(Journey $journey)
    {
        $this->journe;
    }
}
