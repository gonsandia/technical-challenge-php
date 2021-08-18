<?php

namespace Gonsandia\CarPoolingChallenge\Infrastructure\UI\Web;

use Gonsandia\CarPoolingChallenge\Application\Service\PerformJourney\PerformJourneyRequest;
use Gonsandia\CarPoolingChallenge\Application\Service\PerformJourney\PerformJourneyService;
use Gonsandia\CarPoolingChallenge\Application\Service\TransactionalApplicationService;
use Gonsandia\CarPoolingChallenge\Application\Service\TransactionalSession;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
use Gonsandia\CarPoolingChallenge\Domain\Model\TotalPeople;
use Gonsandia\CarPoolingChallenge\Infrastructure\Exception\BodyDeserializationException;
use Gonsandia\CarPoolingChallenge\Infrastructure\Exception\InvalidContentTypeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PerformJourneyController extends AbstractController
{
    public const JSON_CONTENT_TYPE = 'json';

    private PerformJourneyService $journeyService;
    private TransactionalSession $session;

    public function __construct(PerformJourneyService $journeyService, TransactionalSession $session)
    {
        $this->journeyService = $journeyService;
        $this->session = $session;
    }

    public function index(Request $request): Response
    {
        $this->assertContentType($request, self::JSON_CONTENT_TYPE);

        $action = $this->serializeRequest($request);

        $transactionalService = new TransactionalApplicationService(
            $this->journeyService,
            $this->session
        );

        $journey = $transactionalService->execute($action);

        if (is_null($journey->getCarId())) {
            return new Response(null, Response::HTTP_ACCEPTED);
        }

        return new Response(null, Response::HTTP_OK);
    }

    private function assertContentType(Request $request, string $contentType): void
    {
        if ($request->getContentType() !== $contentType) {
            throw new InvalidContentTypeException();
        }
    }

    private function serializeRequest(Request $request): PerformJourneyRequest
    {
        $body = $this->serializeBody($request);

        return new PerformJourneyRequest(
            new JourneyId((int)$body['id']),
            new TotalPeople((int)$body['people'])
        );
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