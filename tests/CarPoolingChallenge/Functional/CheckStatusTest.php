<?php

namespace Gonsandia\Tests\CarPoolingChallenge\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CheckStatusTest extends WebTestCase
{
    /**
     * @test use case
     */
    public function given_any_when_any_then_ok(): void
    {
        $client = self::createClient();

        $client->request(
            'GET',
            '/status',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        $code = $client->getResponse()->getStatusCode();

        self::assertEquals(Response::HTTP_OK, $code);
    }
}
