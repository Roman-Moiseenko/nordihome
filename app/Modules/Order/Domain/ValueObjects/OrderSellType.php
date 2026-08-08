<?php
declare(strict_types=1);

namespace App\Modules\Order\Domain\ValueObjects;

use InvalidArgumentException;

final class OrderSellType
{
    public const string ONLINE = 'online';
    public const string MANUAL = 'manual';
    public const string OZON = 'ozon';
    public const string AVITO = 'avito';

    public const array TYPES = [
        self::ONLINE => 'Интернет-магазин',
        self::MANUAL => 'Менеджер',
        self::OZON => 'Озон',
        self::AVITO => 'Авито',
    ];

    private const array ALLOWED = [
        self::ONLINE,
        self::MANUAL,
        self::OZON,
        self::AVITO,
    ];

    private ?string $value;

    public function __construct(string $value)
    {
        $normalized = strtolower(trim($value));
        if (!in_array($normalized, self::ALLOWED, true)) {
            throw new InvalidArgumentException("Недопустимое значение типа продажи: {$value}");
        }
        $this->value = $normalized;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function label(): string
    {
        return self::TYPES[$this->value] ?? '';
    }

    public function __toString(): string
    {
        return $this->value ?? '';
    }
}
