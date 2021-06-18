<?php

namespace Gonsandia\CarPoolingChallenge\Application\Service\LoadAvailableCars;

class LoadAvailableCarsResponse
{
    private array $cars;

    public function __construct(array $cars)
    {
        $this->cars = $cars;
    }

    public function getCars(): array
    {
        return $this->cars;
    }
}
