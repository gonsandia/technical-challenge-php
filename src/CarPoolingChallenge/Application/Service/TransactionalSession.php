<?php

namespace Gonsandia\CarPoolingChallenge\Application\Service;

interface TransactionalSession
{
    /**
     * @param  callable $operation
     * @return mixed
     */
    public function executeAtomically(callable $operation);
}
