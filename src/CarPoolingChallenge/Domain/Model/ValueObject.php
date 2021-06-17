<?php

declare(strict_types=1);

namespace Gonsandia\CarPoolingChallenge\Domain\Model;

interface ValueObject extends \Stringable
{
    public function equals($other): bool;
}
