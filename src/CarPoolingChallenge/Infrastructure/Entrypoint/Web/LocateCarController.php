<?php

namespace Gonsandia\CarPoolingChallenge\Infrastructure\Entrypoint\Web;

use Gonsandia\CarPoolingChallenge\Application\Service\LocateCar\LocateCarRequest;
use Gonsandia\CarPoolingChallenge\Application\Service\LocateCar\LocateCarService;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
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

        $action = $this->serializeRequest($request);

        $car = $this->locateCarService->execute($action);

        return $this->json(['id' => $car->getCarId(), 'seats' => $car->getTotalSeats()], Response::HTTP_OK, ['content-type' => 'application/json']);
    }

    private function assertContentType(Request $request, string $contentType): void
    {
        if ($request->getContentType() !== $contentType) {
            throw new InvalidContentTypeException();
        }
    }

    private function serializeRequest(Request $request): LocateCarRequest
    {
        return new LocateCarRequest(
            new JourneyId($request->get('ID'))
        );
    }
}
