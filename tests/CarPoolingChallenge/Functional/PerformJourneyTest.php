<?php

namespace Gonsandia\Tests\CarPoolingChallenge\Functional;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PerformJourneyTest extends WebTestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @test use case
     */
    public function given_perform_journey_when_wait_for_car_then_ok()
    {
        $this->setCarsWithoutEmptySeatsState();

        self::ensureKernelShutdown();
        $client = PerformJourneyTest::createClient();

        $data =
            [
                "id" => 1,
                "people" => 4
            ];

        $client->request(
            'POST',
            '/journey',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            Utils::jsonEncode($data)
        );

        $code = $client->getResponse()->getStatusCode();

        $this->assertEquals(Response::HTTP_OK, $code);
    }

    /**
     * @test use case
     */
    public function given_perform_journey_when_found_car_then_accepted()
    {
        $this->setCarsWithEmptySeatsState();

        self::ensureKernelShutdown();
        $client = static::createClient();

        $data =
            [
                "id" => 1,
                "people" => 4
            ];

        $client->request(
            'POST',
            '/journey',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            Utils::jsonEncode($data)
        );

        $code = $client->getResponse()->getStatusCode();

        $this->assertEquals(Response::HTTP_ACCEPTED, $code);
    }

    /**
     * Create DB_STATE WITH empty journeys and available cars
     *
     * @throws ORMException
     */
    private function setCarsWithEmptySeatsState()
    {
//        /** @var CarRepository $carRepository */
//        $carRepository = $this->entityManager->getRepository(Car::class);
//        /** @var JourneyRepository $journeyRepository */
//        $journeyRepository = $this->entityManager->getRepository(Journey::class);
//
//        // clear tables
//        $carRepository->clearTable();
//        $journeyRepository->clearTable();
//
//        // add cars with available seats
//        $car1 = Car::fromTestingValues(1, 3, 3);
//        $car2 = Car::fromTestingValues(2, 6, 6);
//        $carRepository->saveCars([$car1, $car2]);
    }

    /**
     * Create DB_STATE WITH empty journeys and without available cars
     *
     * @throws ORMException
     */
    private function setCarsWithoutEmptySeatsState()
    {
//        /** @var CarRepository $carRepository */
//        $carRepository = $this->entityManager->getRepository(Car::class);
//        /** @var JourneyRepository $journeyRepository */
//        $journeyRepository = $this->entityManager->getRepository(Journey::class);
//
//        // clear tables
//        $carRepository->clearTable();
//        $journeyRepository->clearTable();
//
//        // add cars without available seats
//        $car1 = Car::fromTestingValues(1, 0, 3);
//        $car2 = Car::fromTestingValues(2, 0, 3);
//        $carRepository->saveCars([$car1, $car2]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
