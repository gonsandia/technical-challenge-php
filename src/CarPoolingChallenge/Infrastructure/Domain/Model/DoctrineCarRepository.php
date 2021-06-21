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
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalPeople;

class DoctrineCarRepository extends ServiceEntityRepository implements CarRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Car::class);
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

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function ofId(CarId $carId): Car
    {
        $car = $this->findOneBy(['carId' => $carId]);

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

        return $qb->getQuery()->getOneOrNullResult();
    }
}
