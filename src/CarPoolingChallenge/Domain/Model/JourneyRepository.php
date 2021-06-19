<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Model;

interface JourneyRepository
{
    public function save(Journey $journey): void;

    public function remove(Journey $journey): void;

    public function ofId(JourneyId $journeyId): Journey;

    public function findPendingJourneyForCar(Car $car): ?Journey;
}
