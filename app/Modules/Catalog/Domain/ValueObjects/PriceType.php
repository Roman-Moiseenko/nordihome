<?php

namespace App\Modules\Catalog\Domain\ValueObjects;

use InvalidArgumentException;

final class PriceType
{
    public const string RETAIL = 'retail';
    public const string BULK = 'bulk';
    public const string MINIMAL = 'minimal';
    public const string SPECIAL = 'special';
    public const string COST = 'cost';

    public const string PREORDER = 'preorder';

    public const array TYPES = [
        self::RETAIL => 'Розничная',
        self::BULK => 'Оптовая',
        self::MINIMAL => 'Минимальная',
        self::SPECIAL => 'Специальная',
        self::COST => 'Закупочная',
        self::PREORDER => 'Под заказ',
    ];
    private const array ALLOWED_VALUES = [
        self::RETAIL,
        self::BULK,
        self::MINIMAL,
        self::SPECIAL,
        self::COST,
        self::PREORDER,
    ];

    public string $value {
        get {
            return $this->value;
        }
    }

    public function __construct(?string $value)
    {
        if (is_null($value)) {
            $this->value = self::RETAIL;
        }  else {
            $this->value = $value;
        }
    }

    // --- Именованные фабрики ---

    public static function retail(): self
    {
        return new self(self::RETAIL);
    }

    //TODO нужны ли остальные


    public static function fromString(string $value): self
    {
        return new self($value);
    }

    // --- Геттер ---

    // --- Иммутабельные методы (для совместимости со стилем) ---


    // --- Сравнение ---

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    // --- Часто используемые проверки ---

    //TODO возможно написать is... ()

    // --- Для отладки ---

    public function __toString(): string
    {
        return $this->value;
    }

    // --- Значение по умолчанию (если нужно) ---

    public static function default(): self
    {
        return self::retail();
    }
}
