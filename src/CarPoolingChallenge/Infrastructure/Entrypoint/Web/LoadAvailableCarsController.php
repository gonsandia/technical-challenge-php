<?php

namespace Gonsandia\CarPoolingChallenge\Infrastructure\Entrypoint\Web;

use Gonsandia\CarPoolingChallenge\Application\Service\LoadAvailableCars\LoadAvailableCarsRequest;
use Gonsandia\CarPoolingChallenge\Application\Service\LoadAvailableCars\LoadAvailableCarsService;
use Gonsandia\CarPoolingChallenge\Infrastructure\Exception\InvalidContentTypeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LoadAvailableCarsController extends AbstractController
{
    public const FORM_CONTENT_TYPE = 'form';

    private LoadAvailableCarsService $loadAvailableCars;

    /**
     * LoadAvailableCars constructor.
     */
    public function __construct(LoadAvailableCarsService $loadAvailableCars)
    {
        $this->loadAvailableCars = $loadAvailableCars;
    }


    public function index(Request $request): Response
    {
        $this->assertContentType($request, self::FORM_CONTENT_TYPE);

        $request = $this->serializeRequest($request);

        $this->loadAvailableCars->execute($request);

        return new Response(null, Response::HTTP_OK);
    }

    /**
     * @throws InvalidContentTypeException
     */
    private function assertContentType(Request $request, string $contentType): void
    {
        if ($request->getContentType() !== $contentType) {
            throw new InvalidContentTypeException();
        }
    }

    private function serializeRequest(Request $request): LoadAvailableCarsRequest
    {
        return new LoadAvailableCarsRequest();
    }
}
