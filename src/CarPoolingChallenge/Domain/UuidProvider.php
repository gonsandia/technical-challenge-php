<?php

namespace Gonsandia\CarPoolingChallenge\Domain;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidProvider
{
    private static ?UuidProvider $instance = null;

    private UuidInterface $id;

    public static function instance(): UuidProvider
    {
        if (null === static::$instance) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    private function __construct()
    {
        $this->id = $this->getUUID();
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getUUID()
    {
        return Uuid::uuid4();
    }
}
