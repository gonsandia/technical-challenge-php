<?php

namespace Gonsandia\Tests\CarPoolingChallenge\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoadAvailableCarsTest extends WebTestCase
{
    /**
     * @test use case
     */
    public function given_array_cars_when_loaded_success_then_ok()
    {
        $client = self::createClient();

        $data = [
            [
                "id" => 1,
                "seats" => 4
            ],
            [
                "id" => 2,
                "seats" => 6
            ]
        ];

        $client->request(
            'PUT',
            '/cars',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data, JSON_THROW_ON_ERROR)
        );

        $code = $client->getResponse()->getStatusCode();

        self::assertEquals(Response::HTTP_OK, $code);
    }

    /**
     * @test hardness
     */
    public function given_many_array_cars_when_loaded_success_then_ok()
    {
        $client = static::createClient();

        $data = [];

        for ($i = 1; $i < 10000; $i++) {
            $data [] =
                [
                    "id" => $i,
                    "seats" => random_int(4, 6)
                ];
        }

        $client->request(
            'PUT',
            '/cars',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data, JSON_THROW_ON_ERROR)
        );

        $code = $client->getResponse()->getStatusCode();

        self::assertEquals(Response::HTTP_OK, $code);
    }
}
