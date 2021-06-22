<?php

namespace Gonsandia\CarPoolingChallenge\Application\Service\LoadAvailableCars;

use Gonsandia\CarPoolingChallenge\Domain\Model\Car;

class LoadAvailableCarsRequest
{
    /** @var Car[] array  */
    private array $cars;

    public function __construct(array $cars)
    {
        $this->cars = $cars;
    }

    /** return Car[] array  */
    public function getCars(): array
    {
        return $this->cars;
    }
}
