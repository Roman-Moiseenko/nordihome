<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\ValueObjects;

use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * VisitorUuid — постоянный идентификатор посетителя из cookie.
 *
 * Генерируется при первом заходе (JS или сервером), хранится в CHAR(36).
 */
final class VisitorUuid
{
    private string $value;

    public function __construct(string $value)
    {
        $normalized = strtolower(trim($value));

        if (!self::isValid($normalized)) {
            throw new InvalidArgumentException("Некорректный UUID посетителя: {$value}");
        }

        $this->value = $normalized;
    }

    public static function generate(): self
    {
        return new self((string)Str::uuid());
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public static function isValid(string $value): bool
    {
        return preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            strtolower(trim($value))
        ) === 1;
    }
}
