<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Model;

class CarId implements \Stringable
{
    private int $value;

    public function value(): int
    {
        return $this->value;
    }

    public function __construct(int $value)
    {
        $this->value = $value;
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
