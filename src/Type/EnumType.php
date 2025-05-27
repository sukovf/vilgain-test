<?php

namespace App\Type;

use App\Type\Exception\InvalidArgumentException;
use BackedEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use TypeError;
use ValueError;

abstract class EnumType extends Type
{
    /**
     * @inheritDoc
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = $column['length'] ?? 255;
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): string|int
    {
        if (!($value instanceof BackedEnum)) {
            throw new InvalidArgumentException(sprintf('Expected a backed enum, got "%s"', gettype($value)));
        }

        return $value->value;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): BackedEnum
    {
        if (!is_int($value) && !is_string($value)) {
            throw new InvalidArgumentException(sprintf('Expected an int or string, git "%s"', gettype($value)));
        }

        $class = $this->getEnumClass();

        try {
            return $class::from($value);
        } catch (ValueError|TypeError) {
            throw new InvalidArgumentException(sprintf('Invalid value "%s" for enum "%s"', $value, $class));
        }
    }

    /**
     * @return class-string<BackedEnum>
     */
    abstract protected function getEnumClass(): string;
}