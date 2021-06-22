<?php

namespace Gonsandia\CarPoolingChallenge\Infrastructure\Entrypoint\Web;

use Gonsandia\CarPoolingChallenge\Application\Service\LoadAvailableCars\LoadAvailableCarsRequest;
use Gonsandia\CarPoolingChallenge\Application\Service\LoadAvailableCars\LoadAvailableCarsService;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarId;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalSeats;
use Gonsandia\CarPoolingChallenge\Infrastructure\Exception\InvalidContentTypeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LoadAvailableCarsController extends AbstractController
{
    public const FORM_CONTENT_TYPE = 'form';

    private LoadAvailableCarsService $loadAvailableCars;

    public function __construct(LoadAvailableCarsService $loadAvailableCars)
    {
        $this->loadAvailableCars = $loadAvailableCars;
    }

    public function index(Request $request): Response
    {
        $this->assertContentType($request, self::FORM_CONTENT_TYPE);

        $action = $this->serializeRequest($request);

        $this->loadAvailableCars->execute($action);

        return new Response(null, Response::HTTP_OK);
    }

    private function assertContentType(Request $request, string $contentType): void
    {
        if ($request->getContentType() !== $contentType) {
            throw new InvalidContentTypeException();
        }
    }

    private function serializeRequest(Request $request): LoadAvailableCarsRequest
    {
        $cars = $this->loadCarsFromArray($request->get('cars'));

        return new LoadAvailableCarsRequest(
            $cars
        );
    }

    private function loadCarsFromArray(array $cars): array
    {
        $loadedCars = [];
        foreach ($cars as $car) {
            $loadedCars[$car['id']] = Car::from(
                new CarId($car['id']),
                new TotalSeats($car['seats'])
            );
        }

        return $loadedCars;
    }
}
