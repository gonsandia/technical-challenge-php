<?php

namespace Gonsandia\Tests\CarPoolingChallenge\Unit\Application\Service\LoadAvailableCars;

use Assert\InvalidArgumentException;
use Gonsandia\CarPoolingChallenge\Application\Service\LoadAvailableCars\LoadAvailableCarsService;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarId;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalSeats;
use PHPUnit\Framework\TestCase;

class LoadAvailableCarsServiceTest extends TestCase
{
    private CarRepository $carRepository;

    protected function setUp(): void
    {
        $mockCarRepository = $this->createMock(CarRepository::class);
        $this->carRepository = $mockCarRepository;
        parent::setUp();
    }

    public function testShouldCreateAvailableCarListFromRequest(): void
    {
        $request = new LoadAvailableCarsRequest(
            [
                Car::from(
                    new CarId(1),
                    new TotalSeats(4)
                ),
                Car::from(
                    new CarId(2),
                    new TotalSeats(5)
                ),
                Car::from(
                    new CarId(3),
                    new TotalSeats(5)
                ),
                Car::from(
                    new CarId(4),
                    new TotalSeats(5)
                ),
                Car::from(
                    new CarId(5),
                    new TotalSeats(6)
                ),
            ]
        );

        $service = new LoadAvailableCarsService($this->carRepository);

        $cars = $service->execute($request);

        self::assertNotEmpty($cars);
    }

    public function testShouldNotCreateAvailableCarListFromRequestInvalidNumberOfSeats(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $request = new LoadAvailableCarsRequest(
            [
                Car::from(
                    new CarId(1),
                    new TotalSeats(0)
                ),
                Car::from(
                    new CarId(2),
                    new TotalSeats(5)
                ),
                Car::from(
                    new CarId(3),
                    new TotalSeats(5)
                ),
                Car::from(
                    new CarId(4),
                    new TotalSeats(5)
                ),
                Car::from(
                    new CarId(5),
                    new TotalSeats(126)
                ),
            ]
        );

        $service = new LoadAvailableCarsService($this->carRepository);

        $service->execute($request);
    }

    public function testShouldNotCreateAvailableCarListFromRequestInvalidIds(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $request = new LoadAvailableCarsRequest(
            [
                Car::from(
                    new CarId(0),
                    new TotalSeats(4)
                ),
                Car::from(
                    new CarId(2),
                    new TotalSeats(5)
                ),
                Car::from(
                    new CarId(3),
                    new TotalSeats(5)
                ),
                Car::from(
                    new CarId(4),
                    new TotalSeats(5)
                ),
                Car::from(
                    new CarId(-5),
                    new TotalSeats(6)
                ),
            ]
        );

        $service = new LoadAvailableCarsService($this->carRepository);

        $service->execute($request);
    }
}
