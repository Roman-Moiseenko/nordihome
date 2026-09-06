<?php

namespace App\Modules\Guide\Domain\ValueObjects;

final class AdditionType
{
    public const string DELIVERY = 'delivery';
    public const string PACKING = 'packing';
    public const string ASSEMBLY = 'assembly';
    public const string LIFTING = 'lifting';
    public const string OTHER = 'other';


    const array TYPES = [
        self::DELIVERY => 'Доставка', //Автоматическая для Польши, По городу - фиксированная, ТК - ручная
        self::PACKING => 'Упаковка', //Автоматическая будет
        self::LIFTING => 'Подъем', //Ручная ... или автомат расчет за этаж два Вида, Подьем по лестнице, Подьем лифтом
        self::ASSEMBLY => 'Сборка', //Автоматическая по указаным товарам
        self::OTHER => 'Другое',
    ];


    public string $value {
        get {
            return $this->value;
        }
    }


    public function __construct(?string $value)
    {
        if (is_null($value)) {
            $this->value = self::OTHER;
        }  else {
            $this->value = $value;
        }
    }

    private static function delivery(): self
    {
        return new self(self::DELIVERY);
    }
    private static function packing(): self
    {
        return new self(self::PACKING);
    }
    private static function assembly(): self
    {
        return new self(self::ASSEMBLY);
    }
    private static function other(): self
    {
        return new self(self::OTHER);
    }
    private static function lifting(): self
    {
        return new self(self::LIFTING);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function default(): self
    {
        return self::other();
    }
}
