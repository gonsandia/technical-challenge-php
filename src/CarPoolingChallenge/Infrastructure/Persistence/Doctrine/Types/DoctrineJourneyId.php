<?php

declare(strict_types=1);

namespace Gonsandia\CarPoolingChallenge\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Gonsandia\CarPoolingChallenge\Domain\Model\JourneyId;

final class DoctrineJourneyId extends Type
{
    private const MY_TYPE = 'JourneyId';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $className = JourneyId::class;
        return new $className($value);
    }
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value ?? $value->getId();
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    public function getName(): string
    {
        return self::MY_TYPE;
    }
}
