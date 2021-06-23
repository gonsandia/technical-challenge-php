<?php

namespace Gonsandia\CarPoolingChallenge\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Gonsandia\CarPoolingChallenge\Application\Service\TransactionalSession;

class DoctrineSession implements TransactionalSession
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function executeAtomically(callable $operation)
    {
        return $this->entityManager->transactional($operation);
    }
}
