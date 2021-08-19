<?php

namespace Gonsandia\CarPoolingChallenge\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Gonsandia\CarPoolingChallenge\Domain\Exception\CarNotExistsException;
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
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function save(Car $car): void
    {
        if ($this->update($car)) {
            return;
        }

        $this->_em->persist($car);
    }

    private function update(Car $car)
    {
        $qb = $this->createQueryBuilder('c');

        $qb
            ->update(Car::class, 'c')
            ->set('c.availableSeats.value', ':availableSeats')
            ->set('c.totalSeats.value', ':totalSeats')
            ->andWhere($qb->expr()->eq('c.carId ', ':carId'))
            ->setParameter('availableSeats', $car->getAvailableSeats()->value())
            ->setParameter('totalSeats', $car->getTotalSeats()->value())
            ->setParameter('carId', $car->getCarId());

        $result = $qb->getQuery()->execute();

        return 1 == $result;
    }

    public function clearTable(): mixed
    {
        $qb = $this->createQueryBuilder('c');

        $qb->delete();

        return $qb->getQuery()->execute();
    }


    public function findCarForPeople(TotalPeople $totalPeople): ?Car
    {
        $qb = $this->createQueryBuilder('c');

        $qb->select();

        $qb
            ->andWhere($qb->expr()->gte('c.availableSeats.value', ':availableSeats'));

        $qb
            ->setParameter('availableSeats', $totalPeople->value());

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

        if (is_null($car)) {
            throw new CarNotExistsException();
        }

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

    public function flush()
    {
        $this->_em->flush();
    }
}
