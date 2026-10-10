<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\ValueObjects;

use InvalidArgumentException;

final class ReviewStatus
{
    public const string DRAFT = 'draft';
    public const string MODERATED = 'moderated';
    public const string PUBLISHED = 'published';
    public const string BLOCKED = 'blocked';

    public const array LABELS = [
        self::DRAFT => 'Черновик',
        self::MODERATED => 'На модерации',
        self::PUBLISHED => 'Опубликован',
        self::BLOCKED => 'Заблокирован',
    ];

    public const array ALLOWED_VALUES = [
        self::DRAFT,
        self::MODERATED,
        self::PUBLISHED,
        self::BLOCKED,
    ];

    private string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, self::ALLOWED_VALUES, true)) {
            throw new InvalidArgumentException('Неизвестный статус отзыва: ' . $value);
        }

        $this->value = $value;
    }

    public static function draft(): self
    {
        return new self(self::DRAFT);
    }

    public static function moderated(): self
    {
        return new self(self::MODERATED);
    }

    public static function published(): self
    {
        return new self(self::PUBLISHED);
    }

    public static function blocked(): self
    {
        return new self(self::BLOCKED);
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
        return self::LABELS[$this->value];
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function isDraft(): bool
    {
        return $this->value === self::DRAFT;
    }

    public function isModerated(): bool
    {
        return $this->value === self::MODERATED;
    }

    public function isPublished(): bool
    {
        return $this->value === self::PUBLISHED;
    }

    public function isBlocked(): bool
    {
        return $this->value === self::BLOCKED;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
