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
        return is_null($value) ? null : new $className((int)$value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return is_null($value) ? null : (int)$value->value();
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
