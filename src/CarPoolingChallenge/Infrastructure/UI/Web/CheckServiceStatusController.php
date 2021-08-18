<?php

namespace Gonsandia\CarPoolingChallenge\Infrastructure\UI\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckServiceStatusController extends AbstractController
{
    public function index(): Response
    {
        return $this->json(null, Response::HTTP_OK);
    }
}
