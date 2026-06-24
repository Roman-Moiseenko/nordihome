<?php

namespace App\Modules\Auth\Domain\ValueObjects;

use InvalidArgumentException;

final class StaffPosition
{
    public const string SUPERVISOR = 'supervisor';               // Руководитель
    public const string ADMINISTRATOR = 'administrator';               // Руководитель
    public const string CUSTOMER_MANAGER = 'customer_manager';   // Менеджер по работе с клиентами
    public const string PURCHASE_MANAGER = 'purchase_manager';   // Менеджер по закупкам
    public const string DRIVER = 'driver';                       // Водитель
    public const string ASSEMBLER = 'assembler';                 // Сборщик
    public const string LOGIST = 'logist';                       // Логист

    private const array ALLOWED = [
        self::SUPERVISOR,
        self::ADMINISTRATOR,
        self::CUSTOMER_MANAGER,
        self::PURCHASE_MANAGER,
        self::DRIVER,
        self::ASSEMBLER,
        self::LOGIST,
    ];

    private const array POSITIONS = [
        self::SUPERVISOR => "Руководитель",
        self::ADMINISTRATOR => "Администратор",
        self::CUSTOMER_MANAGER => "Менеджер",
        self::PURCHASE_MANAGER => "Менеджер по закупкам",
        self::DRIVER => "Водитель",
        self::ASSEMBLER => "Сборщик",
        self::LOGIST => "Логист",
    ];

    private string $value;

    public function __construct(string $value)
    {
        $normalized = strtolower(trim($value));
        if (!in_array($normalized, self::ALLOWED, true)) {
            throw new InvalidArgumentException("Недопустимая должность: {$value}");
        }
        $this->value = $normalized;
    }

    public function getValue(): string { return $this->value; }
    public function __toString(): string { return $this->value; }
    public function equals(self $other): bool { return $this->value === $other->value; }

    // Удобные проверки
    public function isSupervisor(): bool { return $this->value === self::SUPERVISOR; }
    public function isCustomerManager(): bool { return $this->value === self::CUSTOMER_MANAGER; }
    public function isPurchaseManager(): bool { return $this->value === self::PURCHASE_MANAGER; }
    public function isDriver(): bool { return $this->value === self::DRIVER; }
    public function isAssembler(): bool { return $this->value === self::ASSEMBLER; }
    public function isLogist(): bool { return $this->value === self::LOGIST; }

    /**
     * Возвращает список всех допустимых должностей для отображения в интерфейсе.
     */
    public static function allowed(): array
    {
        return self::ALLOWED;
    }

    public static function positions(): array
    {
        return self::POSITIONS;
    }

    // Статические фабрики для удобства
    public static function supervisor(): self { return new self(self::SUPERVISOR); }
    public static function customerManager(): self { return new self(self::CUSTOMER_MANAGER); }
    public static function purchaseManager(): self { return new self(self::PURCHASE_MANAGER); }
    public static function driver(): self { return new self(self::DRIVER); }
    public static function assembler(): self { return new self(self::ASSEMBLER); }
    public static function logist(): self { return new self(self::LOGIST); }
}
