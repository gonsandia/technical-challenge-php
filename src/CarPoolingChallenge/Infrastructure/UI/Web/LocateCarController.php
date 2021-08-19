<?php

namespace Gonsandia\CarPoolingChallenge\Infrastructure\UI\Web;

use Assert\Assert;
use Gonsandia\CarPoolingChallenge\Application\Service\LocateCar\LocateCarService;
use Gonsandia\CarPoolingChallenge\Infrastructure\Exception\InvalidContentTypeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LocateCarController extends AbstractController
{
    public const FORM_CONTENT_TYPE = 'form';

    private LocateCarService $locateCarService;

    /**
     * LocateCarController constructor.
     * @param LocateCarService $locateCarService
     */
    public function __construct(LocateCarService $locateCarService)
    {
        $this->locateCarService = $locateCarService;
    }

    public function index(Request $request): Response
    {
        $this->assertContentType($request, self::FORM_CONTENT_TYPE);

        $payload = $this->serializeRequest($request);

        $car = $this->locateCarService->execute($payload);

        if (is_null($car)) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return $this->json(['id' => $car->getCarId()->value(), 'seats' => $car->getTotalSeats()->value()], Response::HTTP_OK, ['content-type' => 'application/json']);
    }

    private function assertContentType(Request $request, string $contentType): void
    {
        if ($request->getContentType() !== $contentType) {
            throw new InvalidContentTypeException();
        }
    }

    private function serializeRequest(Request $request): array
    {
        $payload = [];

        $payload['journey_id'] = (int) $request->request->get('ID');

        Assert::that($payload['journey_id'])->integer()->greaterThan(0);

        return $payload;
    }
}
