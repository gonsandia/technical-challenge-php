<?php

namespace Gonsandia\Tests\CarPoolingChallenge\Unit\Application\Service\CheckStatusService;

use Gonsandia\CarPoolingChallenge\Application\Service\CheckStatusService\CheckStatusService;
use PHPUnit\Framework\TestCase;

class CheckStatusServiceTest extends TestCase
{
    /**
     * @test
     */
    public function ItShouldPass(): void
    {
        $service = new CheckStatusService();
        $result = $service->execute();

        self::assertTrue($result);
    }
}
