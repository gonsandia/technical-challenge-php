<?php

declare(strict_types=1);

namespace Gonsandia\CarPoolingChallenge\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Gonsandia\CarPoolingChallenge\Domain\Model\CarId;

final class DoctrineCarId extends Type
{
    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $className = $this->getNamespace() . '\\' . $this->getName();
        return new $className($value);
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value->value();
    }

    private const MY_TYPE = 'CarId';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    public function getName(): string
    {
        return self::MY_TYPE;
    }

    protected function getNamespace(): string
    {
        return CarId::class;
    }
}
