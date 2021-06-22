<?php

namespace Gonsandia\CarPoolingChallenge\Infrastructure\Entrypoint\Web;

use Gonsandia\CarPoolingChallenge\Application\Service\DropOff\DropOffRequest;
use Gonsandia\CarPoolingChallenge\Application\Service\DropOff\DropOffService;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DropOffController extends AbstractController
{
    public const FORM_CONTENT_TYPE = 'form';

    private DropOffService $dropOffService;

    public function __construct(DropOffService $dropOffService)
    {
        $this->dropOffService = $dropOffService;
    }

    public function index(Request $request): Response
    {
        $this->assertContentType($request, self::FORM_CONTENT_TYPE);

        $action = $this->serializeRequest($request);

        $this->dropOffService->execute($action);

        return new Response(null, Response::HTTP_OK);
    }

    private function assertContentType(Request $request, string $contentType): void
    {
        if ($request->getContentType() !== $contentType) {
            throw new \Gonsandia\CarPoolingChallenge\Infrastructure\Exception\InvalidContentTypeException();
        }
    }

    private function serializeRequest(Request $request): DropOffRequest
    {
        return new DropOffRequest(
            new JourneyId((int)$request->get('ID'))
        );
    }
}
