<?php

declare(strict_types=1);

namespace Gonsandia\CarPoolingChallenge\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarId;

final class DoctrineCarId extends Type
{
    private const MY_TYPE = 'CarId';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $className = CarId::class;
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
