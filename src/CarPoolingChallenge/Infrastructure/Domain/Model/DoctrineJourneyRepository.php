<?php

namespace Gonsandia\CarPoolingChallenge\Infrastructure\Domain\Model;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\Journey;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyRepository;

class DoctrineJourneyRepository extends ServiceEntityRepository implements JourneyRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Journey::class);
    }

    public function save(Journey $journey): void
    {
        $this->_em->persist($journey);

        $this->_em->flush();
    }

    public function remove(Journey $journey): void
    {
        $this->_em->remove($journey);

        $this->_em->flush();
    }

    public function ofId(JourneyId $journeyId): Journey
    {
        $journey = $this->findOneBy(['journey' => $journeyId]);

        return $journey;
    }

    public function findPendingJourneyForCar(Car $car): ?Journey
    {
        $qb = $this->createQueryBuilder('j');

        $qb->select();

        $qb
            ->andWhere($qb->expr()->gte('j.people', ':people'));

        $qb
            ->setParameter('people', $car->getAvailableSeats());

        $qb
            ->andWhere($qb->expr()->isNull('j.carId'));

        $qb
            ->setMaxResults(1);

        $qb
            ->orderBy('j.createdAt', 'ASC');

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function clearTable(): mixed
    {
        $qb = $this->createQueryBuilder('c');

        $qb->delete();

        return $qb->getQuery()->execute();
    }
}
