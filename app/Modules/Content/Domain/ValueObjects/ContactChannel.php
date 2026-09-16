<?php

namespace App\Modules\Content\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class ContactChannel implements \Stringable
{
    public const string PHONE = 'phone';
    public const string EMAIL = 'email';
    public const string TELEGRAM = 'telegram';
    public const string VK = 'vk';
    public const string MAX = 'max';
    public const string RUTUBE = 'rutube';
    public const string OK = 'ok';
    public const string AVITO = 'avito';
    public const string OZON = 'ozon';
    //public const string INSTAGRAM = 'instagram';

    private const array ALLOWED = [
        self::PHONE, self::EMAIL, self::TELEGRAM,
        self::VK, self::MAX, self::RUTUBE, self::OK,
        self::AVITO, self::OZON,
    ];
    public const array CONTACTS = [
        self::PHONE => 'Телефон',
        self::EMAIL => 'Почта',
        self::TELEGRAM => 'Телеграм',
        self::VK => 'ВКонтакте',
        self::MAX => 'Макс',
        self::RUTUBE => 'Рутуб',
        self::OK => 'Одноклассники',
        self::AVITO => 'Авито',
        self::OZON => 'Озон',
    ];


    private function __construct(public string $value)
    {
        if (!in_array($value, self::ALLOWED, true)) {
            throw new InvalidArgumentException("Invalid ContactChannel: {$value}");
        }
    }

    public static function from(string $value): self
    {
        return new self($value);
    }

    public static function phone(): self
    {
        return new self(self::PHONE);
    }

    public static function email(): self
    {
        return new self(self::EMAIL);
    }

    public static function max(): self
    {
        return new self(self::MAX);
    }

    public static function telegram(): self
    {
        return new self(self::TELEGRAM);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
