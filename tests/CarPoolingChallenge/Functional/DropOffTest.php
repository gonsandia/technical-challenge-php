<?php

namespace Gonsandia\Tests\CarPoolingChallenge\Functional;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DropOffTest extends WebTestCase
{
    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;

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
    public function given_journey_id_when_wait_for_car_then_ok(): void
    {
        $this->setJourneyWithoutCarState();

        self::ensureKernelShutdown();
        $client = self::createClient();

        $data =
            [
                "ID" => 1,
            ];

        $client->request(
            'POST',
            '/dropoff',
            $data,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded']
        );

        $code = $client->getResponse()->getStatusCode();

        self::assertEquals(Response::HTTP_OK, $code);
    }

    /**
     * @test use case
     */
    public function given_journey_id_when_had_assigned_car_then_ok(): void
    {
        $this->setJourneyWithCarState();

        self::ensureKernelShutdown();
        $client = self::createClient();

        $data =
            [
                "ID" => 1,
            ];

        $client->request(
            'POST',
            '/dropoff',
            $data,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded']
        );

        $code = $client->getResponse()->getStatusCode();

        self::assertEquals(Response::HTTP_OK, $code);
    }

    /**
     * @test use case
     */
    public function given_journey_id_when_group_not_found_then_not_found(): void
    {
        $this->setCleanState();

        self::ensureKernelShutdown();
        $client = static::createClient();

        $data =
            [
                "ID" => 1,
            ];

        $client->request(
            'POST',
            '/dropoff',
            $data,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded']
        );

        $code = $client->getResponse()->getStatusCode();

        self::assertEquals(Response::HTTP_NOT_FOUND, $code);
    }

    /**
     * Create DB_STATE WITH a journey with car associated
     *
     * @throws ORMException
     */
    private function setJourneyWithCarState(): void
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
//        // add car
//        $car1 = Car::fromTestingValues(1, 3, 3);
//        $carRepository->saveCars([$car1]);
//
//        // add car
//        $journey1 = Journey::fromTestingValues(1, 3, 1);
//        $journeyRepository->saveJourney($journey1);
    }

    /**
     * Create DB_STATE WITH a journey without car associated
     *
     * @throws ORMException
     */
    private function setJourneyWithoutCarState(): void
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
//        // add car
//        $car1 = Car::fromTestingValues(1, 3, 3);
//        $carRepository->saveCars([$car1]);
//
//        // add car
//        $journey1 = Journey::fromTestingValues(1, 3, null);
//        $journeyRepository->saveJourney($journey1);
    }

    /**
     * Create DB_STATE clean
     *
     */
    private function setCleanState(): void
    {
//        /** @var CarRepository $carRepository */
//        $carRepository = $this->entityManager->getRepository(Car::class);
//        /** @var JourneyRepository $journeyRepository */
//        $journeyRepository = $this->entityManager->getRepository(Journey::class);
//
//        // clear tables
//        $carRepository->clearTable();
//        $journeyRepository->clearTable();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
