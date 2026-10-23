<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\ValueObjects;

/**
 * Названия системных атрибутов каталога, с которыми работают сервисы привязки.
 */
final class AttributeName
{
    public const string COLOR = 'Цвет';
    public const string MATERIAL = 'Материал';

    public const string WHOM = 'Для кого';

    private function __construct(
        private readonly string $value,
    ) {
    }

    public static function color(): self
    {
        return new self(self::COLOR);
    }

    public static function material(): self
    {
        return new self(self::MATERIAL);
    }
    public static function whom(): self
    {
        return new self(self::WHOM);
    }
    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
