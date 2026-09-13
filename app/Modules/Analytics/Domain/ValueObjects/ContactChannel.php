<?php

namespace App\Modules\Analytics\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class ContactChannel implements \Stringable
{
    public const PHONE    = 'phone';
    public const EMAIL    = 'email';
    public const TELEGRAM = 'telegram';
    public const VK       = 'vk';
    public const MAX       = 'max';
    public const RUTUBE  = 'rutube';
    public const OK       = 'ok';

    private const ALLOWED = [
        self::PHONE, self::EMAIL, self::TELEGRAM,
        self::VK, self::MAX, self::RUTUBE, self::OK,
    ];

    private function __construct(public string $value)
    {
        if (!in_array($value, self::ALLOWED, true)) {
            throw new InvalidArgumentException("Invalid ContactChannel: {$value}");
        }
    }

    public static function from(string $value): self { return new self($value); }
    public static function phone(): self    { return new self(self::PHONE); }
    public static function email(): self    { return new self(self::EMAIL); }
    public static function max(): self { return new self(self::MAX); }
    public static function telegram(): self { return new self(self::TELEGRAM); }

    public function equals(self $other): bool { return $this->value === $other->value; }
    public function __toString(): string      { return $this->value; }
}
