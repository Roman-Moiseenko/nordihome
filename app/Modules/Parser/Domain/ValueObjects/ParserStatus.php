<?php

namespace App\Modules\Parser\Domain\ValueObjects;

use InvalidArgumentException;

final class ParserStatus implements \Stringable
{
    private const string NEW = 'new';
    private const string DELETED = 'deleted';
    private const string ERROR = 'error';
    private const string PRICE_CHANGED = 'price_changed';
    private const array ALLOWED = [
        self::NEW,
        self::DELETED,
        self::PRICE_CHANGED,
        self::ERROR,
    ];

    private function __construct(public string $value)
    {
        if (!in_array($value,self::ALLOWED, true)) {
            throw new \InvalidArgumentException("Invalid parser log item status: $value");
        }
    }

    public static function new(): self
    {
        return new self(self::NEW);
    }

    public static function error(): self
    {
        return new self(self::ERROR);
    }

    public static function deleted(): self
    {
        return new self(self::DELETED);
    }

    public static function priceChanged(): self
    {
        return new self(self::PRICE_CHANGED);
    }

    public static function from(string $value): self
    {
        return new self($value);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
