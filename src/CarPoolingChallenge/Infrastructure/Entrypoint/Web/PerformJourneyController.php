<?php

namespace Gonsandia\CarPoolingChallenge\Infrastructure\Entrypoint\Web;

use Gonsandia\CarPoolingChallenge\Application\Service\PerformJourney\PerformJourneyRequest;
use Gonsandia\CarPoolingChallenge\Application\Service\PerformJourney\PerformJourneyService;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalPeople;
use Gonsandia\CarPoolingChallenge\Infrastructure\Exception\InvalidContentTypeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PerformJourneyController extends AbstractController
{
    public const JSON_CONTENT_TYPE = 'json';

    private PerformJourneyService $journeyService;

    public function __construct(PerformJourneyService $journeyService)
    {
        $this->journeyService = $journeyService;
    }

    public function index(Request $request): Response
    {
        $this->assertContentType($request, self::JSON_CONTENT_TYPE);

        $action = $this->serializeRequest($request);

        $journey = $this->journeyService->execute($action);

        if (is_null($journey->getCarId())) {
            return new Response(null, Response::HTTP_OK);
        }

        return new Response(null, Response::HTTP_ACCEPTED);
    }

    private function assertContentType(Request $request, string $contentType): void
    {
        if ($request->getContentType() !== $contentType) {
            throw new InvalidContentTypeException();
        }
    }

    private function serializeRequest(Request $request): PerformJourneyRequest
    {
        $body = json_decode($request->getContent(), true);

        return new PerformJourneyRequest(
            new JourneyId($body['id']),
            new TotalPeople($body['people'])
        );
    }
}
