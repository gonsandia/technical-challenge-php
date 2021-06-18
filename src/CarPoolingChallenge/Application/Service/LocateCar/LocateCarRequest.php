<?php

namespace Gonsandia\CarPoolingChallenge\Application\Service\LocateCar;

use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;

class LocateCarRequest
{
    private JourneyId $journeyId;

    public function __construct(JourneyId $journeyId)
    {
        $this->journeyId = $journeyId;
    }

    /**
     * @return JourneyId
     */
    public function getJourneyId(): JourneyId
    {
        return $this->journeyId;
    }
}
