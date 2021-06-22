<?php

namespace Gonsandia\Tests\CarPoolingChallenge\Functional;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarId;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\Journey;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalPeople;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalSeats;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DropOffTest extends WebTestCase
{
    private ?EntityManager $entityManager;

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
        $this->setDbStatePendingJourney();

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
        $this->setDbStateCarWithJourneyAssigned();

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
     * @test use case
     */
    public function given_invalid_content_type_when_call_drop_off_then_405(): void
    {
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
            ['CONTENT_TYPE' => 'text/plain']
        );

        $code = $client->getResponse()->getStatusCode();

        self::assertEquals(Response::HTTP_UNSUPPORTED_MEDIA_TYPE, $code);
    }

    /**
     * Create DB_STATE WITH a journey with car associated
     *
     * @throws ORMException
     */
    private function setDbStateCarWithJourneyAssigned(): void
    {
        /** @var CarRepository $carRepository */
        $carRepository = $this->entityManager->getRepository(Car::class);
        /** @var JourneyRepository $journeyRepository */
        $journeyRepository = $this->entityManager->getRepository(Journey::class);

        // clear tables
        $carRepository->clearTable();
        $journeyRepository->clearTable();

        // add car
        $car1 = Car::from(new CarId(1), new TotalSeats(4));

        // add journey
        $journey1 = Journey::from(new JourneyId(1), new TotalPeople(4));
        $car1->performJourney($journey1);

        $carRepository->save($car1);
        $journeyRepository->save($journey1);
    }

    /**
     * Create DB_STATE WITH a journey without car associated
     *
     * @throws ORMException
     */
    private function setDbStatePendingJourney(): void
    {
        /** @var CarRepository $carRepository */
        $carRepository = $this->entityManager->getRepository(Car::class);
        /** @var JourneyRepository $journeyRepository */
        $journeyRepository = $this->entityManager->getRepository(Journey::class);

        // clear tables
        $carRepository->clearTable();
        $journeyRepository->clearTable();

        // add car
        $car1 = Car::from(new CarId(1), new TotalSeats(6));
        $journey1 = Journey::from(new JourneyId(1), new TotalPeople(4));

        // persist
        $carRepository->save($car1);
        $journeyRepository->save($journey1);
    }

    /**
     * Create DB_STATE clean
     *
     */
    private function setCleanState(): void
    {
        /** @var CarRepository $carRepository */
        $carRepository = $this->entityManager->getRepository(Car::class);
        /** @var JourneyRepository $journeyRepository */
        $journeyRepository = $this->entityManager->getRepository(Journey::class);

        // clear tables
        $carRepository->clearTable();
        $journeyRepository->clearTable();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
