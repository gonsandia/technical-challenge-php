<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Model;

interface CarRepository
{
    public function ofId(CarId $carId): Car;

    public function save(Car $car): void;

    public function findCarWithEnoughEmptySeatsForGroup(TotalPeople $totalPeople): ?Car;

    public function ofJourneyId(JourneyId $param): ?Car;

    public function loadCars(array $cars);
}
