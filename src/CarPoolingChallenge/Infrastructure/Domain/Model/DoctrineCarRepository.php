<?php

namespace Gonsandia\CarPoolingChallenge\Infrastructure\Domain\Model;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarId;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\Journey;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyRepository;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalPeople;

class DoctrineCarRepository extends ServiceEntityRepository implements CarRepository
{
    private JourneyRepository $journeyRepository;

    public function __construct(ManagerRegistry $registry, JourneyRepository $journeyRepository)
    {
        parent::__construct($registry, Car::class);
        $this->journeyRepository = $journeyRepository;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function loadCars(array $cars): void
    {
        $this->clearTable();

        foreach ($cars as $car) {
            $this->_em->persist($car);
        }
        $this->_em->flush();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function save(Car $car): void
    {
        $this->_em->persist($car);

        $this->_em->flush();
    }

    public function clearTable(): mixed
    {
        $qb = $this->createQueryBuilder('c');

        $qb->delete();

        return $qb->getQuery()->execute();
    }


    public function findCarWithEnoughEmptySeatsForGroup(TotalPeople $totalPeople): ?Car
    {
        $qb = $this->createQueryBuilder('c');

        $qb->select();

        $qb
            ->andWhere($qb->expr()->gte('c.availableSeats', ':availableSeats'));

        $qb
            ->setParameter('availableSeats', $totalPeople->getCount());

        $qb
            ->setMaxResults(1);

        $car = $qb->getQuery()->getOneOrNullResult();

        $this->fillCarWithJourneys($car);

        return $car;
    }

    public function ofId(CarId $carId): Car
    {
        $car = $this->findOneBy(['carId' => $carId]);

        $this->fillCarWithJourneys($car);

        return $car;
    }

    public function ofJourneyId(JourneyId $journeyId): ?Car
    {
        $qb = $this->createQueryBuilder('c');

        $qb->select();

        $qb
            ->leftJoin(Journey::class, 'j', \Doctrine\ORM\Query\Expr\Join::WITH, $qb->expr()->eq('j.carId', 'c.carId'))
            ->andWhere($qb->expr()->eq('j.journeyId', ':journeyId'));

        $qb
            ->setParameter('journeyId', $journeyId);

        $qb
            ->setMaxResults(1);

        $car = $qb->getQuery()->getOneOrNullResult();

        $this->fillCarWithJourneys($car);

        return $car;
    }

    private function fillCarWithJourneys(?Car $car): void
    {
        if (is_null($car)) {
            return;
        }

        $journeys = $this->journeyRepository->ofCarId($car->getCarId());
        $car->setJourneys($journeys);
    }
}
