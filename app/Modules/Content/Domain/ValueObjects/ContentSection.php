<?php

namespace App\Modules\Content\Domain\ValueObjects;

use InvalidArgumentException;

final class ContentSection
{
    public const string CONTENT = 'content';
    public const string BOTTOM_CONTENT = 'bottom-content';

    public const array SECTIONS = [
        self::CONTENT => 'Основной контейнер',
        self::BOTTOM_CONTENT => 'Блок нижний'];

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
    public static function bottomContent(): self { return new self(self::BOTTOM_CONTENT); }

    public function jsonSerialize(): string { return $this->value; }
}
