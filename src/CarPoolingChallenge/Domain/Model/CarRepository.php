<?php

namespace Gonsandia\CarPoolingChallenge\Domain\Model;

interface CarRepository
{
    public function ofId(CarId $carId): Car;

    public function save(Car $car): void;

    public function findCarForPeople(TotalPeople $totalPeople): ?Car;

    public function ofJourneyId(JourneyId $journeyId): ?Car;

    public function loadCars(array $cars);

    public function clearTable();
}
