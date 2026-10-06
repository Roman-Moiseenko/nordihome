<?php

namespace App\Modules\Catalog\Domain\ValueObjects;

use App\Modules\Catalog\Domain\Exceptions\EmptyModificationNameException;
use App\Modules\Catalog\Domain\Exceptions\ModificationNameTooLongException;
use JsonSerializable;
use Stringable;

/**
 * Название модификации.
 *
 * Инварианты:
 *  - не пустое (после trim);
 *  - не длиннее MAX_LENGTH;
 *  - хранится в trim-нутом виде.
 */
final readonly class ModificationName implements JsonSerializable, Stringable
{
    public const int MAX_LENGTH = 255;

    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw new EmptyModificationNameException();
        }

        if (mb_strlen($trimmed) > self::MAX_LENGTH) {
            throw new ModificationNameTooLongException(self::MAX_LENGTH);
        }

        $this->value = $trimmed;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
