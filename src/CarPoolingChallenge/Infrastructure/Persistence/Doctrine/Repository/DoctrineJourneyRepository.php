<?php

namespace Gonsandia\CarPoolingChallenge\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Gonsandia\CarPoolingChallenge\Domain\Exception\CarNotExistsException;
use Gonsandia\CarPoolingChallenge\Domain\Exception\JourneyNotExistsException;
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
        if ($this->update($journey)) {
            return;
        }

        $this->_em->persist($journey);
    }

    private function update(Journey $journey)
    {
        $qb = $this->createQueryBuilder('j');

        $qb
            ->update(Journey::class, 'j')
            ->set('j.totalPeople.value', ':totalPeople')
            ->set('j.carId', ':carId')
            ->andWhere($qb->expr()->eq('j.journeyId', ':journeyId'))
            ->setParameter('totalPeople', $journey->getTotalPeople()->value())
            ->setParameter('carId', $journey->getCarId())
            ->setParameter('journeyId', $journey->getJourneyId());

        $result = $qb->getQuery()->execute();

        return 1 == $result;
    }

    public function remove(Journey $journey): void
    {
        $this->_em->remove($journey);
    }

    public function ofId(JourneyId $journeyId): Journey
    {
        $journey = $this->findOneBy(['journeyId' => $journeyId]);

        if (is_null($journey)) {
            throw new JourneyNotExistsException();
        }

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
        $qb = $this->createQueryBuilder('j');

        $qb->delete();

        return $qb->getQuery()->execute();
    }

    public function ofCarId($carId): array
    {
        $qb = $this->createQueryBuilder('j');

        $qb->select();

        $qb
            ->andWhere($qb->expr()->eq('j.carId', ':carId'));

        $qb
            ->setParameter('carId', $carId);

        $journeys = $qb->getQuery()->getResult();

        return $journeys;
    }

    public function flush() {
        $this->_em->flush();
    }
}
