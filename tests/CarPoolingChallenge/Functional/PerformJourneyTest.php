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
        $this->setDbStateCarWithEmptySeats();

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
            json_encode($data, JSON_THROW_ON_ERROR)
        );

        $code = $client->getResponse()->getStatusCode();

        $this->assertEquals(Response::HTTP_OK, $code);
    }

    /**
     * @test use case
     */
    public function given_perform_journey_when_found_car_then_accepted()
    {
        $this->setDbStateCarWithoutEmptySeats();

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
            json_encode($data, JSON_THROW_ON_ERROR)
        );

        $code = $client->getResponse()->getStatusCode();

        $this->assertEquals(Response::HTTP_ACCEPTED, $code);
    }

    /**
     * Create DB_STATE WITH a journey without car associated
     *
     * @throws ORMException
     */
    private function setDbStateCarWithoutEmptySeats(): void
    {
        /** @var CarRepository $carRepository */
        $carRepository = $this->entityManager->getRepository(Car::class);
        /** @var JourneyRepository $journeyRepository */
        $journeyRepository = $this->entityManager->getRepository(Journey::class);

        // clear tables
        $carRepository->clearTable();
        $journeyRepository->clearTable();

        // add car
        $car1 = Car::from(new CarId(1),new TotalSeats(6));
        $journey1 = Journey::from(new JourneyId(1), new TotalPeople(6));

        // persist
        $carRepository->save($car1);
        $journeyRepository->save($journey1);
    }

    /**
     * Create DB_STATE WITH a journey without car associated
     *
     * @throws ORMException
     */
    private function setDbStateCarWithEmptySeats(): void
    {
        /** @var CarRepository $carRepository */
        $carRepository = $this->entityManager->getRepository(Car::class);
        /** @var JourneyRepository $journeyRepository */
        $journeyRepository = $this->entityManager->getRepository(Journey::class);

        // clear tables
        $carRepository->clearTable();
        $journeyRepository->clearTable();

        // add car
        $car1 = Car::from(new CarId(1),new TotalSeats(6));
        $journey1 = Journey::from(new JourneyId(1), new TotalPeople(1));

        // persist
        $carRepository->save($car1);
        $journeyRepository->save($journey1);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
