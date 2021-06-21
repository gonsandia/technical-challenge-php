<?php

namespace Gonsandia\CarPoolingChallenge\Application\Service\FillCarWithQueueJourneys;

use Gonsandia\CarPoolingChallenge\Domain\Model\CarId;

class FillCarWithQueueJourneysRequest
{
    private CarId $carId;

    public function __construct(CarId $carId)
    {
        $this->carId = $carId;
    }

    /**
     * @return CarId
     */
    public function getCarId(): CarId
    {
        return $this->carId;
    }
}
