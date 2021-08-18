<?php

namespace Gonsandia\CarPoolingChallenge\Infrastructure\UI\Web;

use Gonsandia\CarPoolingChallenge\Application\Service\LoadAvailableCars\LoadAvailableCarsRequest;
use Gonsandia\CarPoolingChallenge\Application\Service\LoadAvailableCars\LoadAvailableCarsService;
use Gonsandia\CarPoolingChallenge\Application\Service\TransactionalApplicationService;
use Gonsandia\CarPoolingChallenge\Application\Service\TransactionalSession;
use Gonsandia\CarPoolingChallenge\Domain\Model\Car;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarId;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalSeats;
use Gonsandia\CarPoolingChallenge\Infrastructure\Exception\BodyDeserializationException;
use Gonsandia\CarPoolingChallenge\Infrastructure\Exception\InvalidContentTypeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LoadAvailableCarsController extends AbstractController
{
    public const FORM_CONTENT_TYPE = 'json';

    private LoadAvailableCarsService $loadAvailableCars;
    private TransactionalSession $session;

    public function __construct(LoadAvailableCarsService $loadAvailableCars, TransactionalSession $session)
    {
        $this->loadAvailableCars = $loadAvailableCars;
        $this->session = $session;
    }

    public function index(Request $request): Response
    {
        $this->assertContentType($request, self::FORM_CONTENT_TYPE);

        $action = $this->serializeRequest($request);

        $transactionalService = new TransactionalApplicationService(
            $this->loadAvailableCars,
            $this->session
        );

        $transactionalService->execute($action);

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
        $body = $this->serializeBody($request);

        $cars = $this->loadCarsFromArray($body);

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

    private function serializeBody(Request $request): mixed
    {
        try {
            return json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Exception $exception) {
            throw new BodyDeserializationException();
        }
    }
}
