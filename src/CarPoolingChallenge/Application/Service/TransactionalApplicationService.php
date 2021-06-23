<?php

namespace Gonsandia\CarPoolingChallenge\Application\Service;

class TransactionalApplicationService implements ApplicationService
{
    private TransactionalSession $session;

    private ApplicationService $service;

    public function __construct(ApplicationService $service, TransactionalSession $session)
    {
        $this->session = $session;
        $this->service = $service;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function execute($request = null)
    {
        if (empty($this->service)) {
            throw new \LogicException('A use case must be specified');
        }

        $operation = function () use ($request) {
            return $this->service->execute($request);
        };

        return $this->session->executeAtomically($operation);
    }
}
