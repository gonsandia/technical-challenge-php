<?php

namespace Gonsandia\CarPoolingChallenge\Infrastructure\UI\Web;

use Assert\Assert;
use Gonsandia\CarPoolingChallenge\Application\Service\DropOff\DropOffService;
use Gonsandia\CarPoolingChallenge\Application\Service\TransactionalApplicationService;
use Gonsandia\CarPoolingChallenge\Application\Service\TransactionalSession;
use Gonsandia\CarPoolingChallenge\Infrastructure\Exception\InvalidContentTypeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DropOffController extends AbstractController
{
    public const FORM_CONTENT_TYPE = 'form';

    private DropOffService $dropOffService;
    private TransactionalSession $session;

    public function __construct(DropOffService $dropOffService, TransactionalSession $session)
    {
        $this->dropOffService = $dropOffService;
        $this->session = $session;
    }

    public function index(Request $request): Response
    {
        $this->assertContentType($request, self::FORM_CONTENT_TYPE);

        $payload = $this->serializeRequest($request);

        $transactionalService = new TransactionalApplicationService(
            $this->dropOffService,
            $this->session
        );

        $transactionalService->execute($payload);

        return new Response(null, Response::HTTP_OK);
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
