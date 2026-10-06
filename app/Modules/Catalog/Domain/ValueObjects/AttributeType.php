<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\ValueObjects;

use InvalidArgumentException;

final class AttributeType
{
    public const string TYPE_STRING = 'string';
    public const string TYPE_INTEGER = 'integer';
    public const string TYPE_BOOL = 'bool';
    public const string TYPE_VARIANT = 'variant';
    public const string TYPE_FLOAT = 'float';
    public const string TYPE_DATE = 'date';

    public const array ATTRIBUTES = [
        self::TYPE_STRING => 'Строка',
        self::TYPE_INTEGER => 'Число',
        self::TYPE_BOOL => 'Флажок',
        self::TYPE_VARIANT => 'Варианты',
        self::TYPE_FLOAT => 'Дробное',
        self::TYPE_DATE => 'Дата',
    ];

    public const array ALLOWED_VALUES = [
        self::TYPE_STRING,
        self::TYPE_INTEGER,
        self::TYPE_BOOL,
        self::TYPE_VARIANT,
        self::TYPE_FLOAT,
        self::TYPE_DATE,
    ];

    private string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, self::ALLOWED_VALUES, true)) {
            throw new InvalidArgumentException('Неизвестный тип атрибута: ' . $value);
        }

        $this->value = $value;
    }

    public function isVariant(): bool
    {
        return $this->value === self::TYPE_VARIANT;
    }

    public static function variant(): self
    {
        return new self(self::TYPE_VARIANT);
    }
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function label(): string
    {
        return self::ATTRIBUTES[$this->value];
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function isString(): bool
    {
        return $this->value === self::TYPE_STRING;
    }

    public function isInteger(): bool
    {
        return $this->value === self::TYPE_INTEGER;
    }

    public function isBool(): bool
    {
        return $this->value === self::TYPE_BOOL;
    }

    public function isFloat(): bool
    {
        return $this->value === self::TYPE_FLOAT;
    }

    public function isDate(): bool
    {
        return $this->value === self::TYPE_DATE;
    }

    public function isNumeric(): bool
    {
        return $this->isInteger() || $this->isFloat();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
