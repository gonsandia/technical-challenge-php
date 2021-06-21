<?php

namespace Gonsandia\CarPoolingChallenge\Application\Service\PerformJourney;

use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalPeople;

class PerformJourneyRequest
{
    private JourneyId $id;

    private TotalPeople $people;

    /**
     * PerformJourneyRequest constructor.
     * @param JourneyId $id
     * @param TotalPeople $people
     */
    public function __construct(JourneyId $id, TotalPeople $people)
    {
        $this->id = $id;
        $this->people = $people;
    }

    /**
     * @return JourneyId
     */
    public function getId(): JourneyId
    {
        return $this->id;
    }

    /**
     * @return TotalPeople
     */
    public function getPeople(): TotalPeople
    {
        return $this->people;
    }
}
