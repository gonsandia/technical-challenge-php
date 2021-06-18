<?php

namespace Gonsandia\CarPoolingChallenge\Infrastructure\Entrypoint\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckServiceStatusController extends AbstractController
{
    /**
     * Entrypoint for Check server status Use case.
     *
     * @Route("/status", name="status", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->json(null, Response::HTTP_OK);
    }
}
