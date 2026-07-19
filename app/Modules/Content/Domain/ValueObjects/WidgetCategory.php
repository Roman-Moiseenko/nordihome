<?php

namespace App\Modules\Content\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * WidgetCategory — перечисление возможных категорий: content, media, commerce, custom.
 */
final class WidgetCategory
{
    public const string CONTENT = 'content';
    public const string CONTAINER = 'container';
    public const string MEDIA = 'media';
    public const string COMMERCE = 'commerce';
    public const string FORM = 'form';
    public const string CUSTOM = 'custom';

    public const array CATEGORIES = [
        self::CONTENT => 'Контент',
        self::MEDIA => 'Медиа',
        self::COMMERCE => 'Магазин',
        self::CONTAINER => 'Контейнеры',
        self::FORM => 'Формы',
        self::CUSTOM => 'Собственный',
    ];
    private string $value;

    public function __construct(string $value)
    {
        $normalized = strtolower(trim($value));

        $this->value = $normalized;
    }

    public function getValue(): string { return $this->value; }
    public function __toString(): string { return $this->value; }
    public function equals(self $other): bool { return $this->value === $other->value; }

    public static function content(): self { return new self(self::CONTENT); }
    public static function media(): self { return new self(self::MEDIA); }
    public static function commerce(): self { return new self(self::COMMERCE); }
    public static function custom(): self { return new self(self::CUSTOM); }
}
