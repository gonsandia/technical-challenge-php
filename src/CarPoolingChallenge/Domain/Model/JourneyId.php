<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Model;

class JourneyId
{

    private int $id;

    public function __construct(int $count)
    {
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
}
