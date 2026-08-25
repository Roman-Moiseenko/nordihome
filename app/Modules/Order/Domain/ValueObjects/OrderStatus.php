<?php

namespace App\Modules\Order\Domain\ValueObjects;

use InvalidArgumentException;

final class OrderStatus
{
    public const string NEW = 'new'; //Новый заказ
    public const string DRAFT = 'draft'; //В работе у менеджера
    public const string AWAITING = 'awaiting'; //Ожидает оплаты - резерв 3 дня ??????
    public const string PREPAID = 'prepaid';  //Предоплата
    public const string PAID = 'paid';  //Оплачен

    public const string SHIPPED = 'partially_shipped'; //Частично выдан
    ///Отмененные статусы
    public const string CANCELLED = 'cancelled';//


    //Завершен
    public const string COMPLETED = 'completed'; //Выдан (завершен)
    public const string COMPLETED_REFUND = 'partially_returned'; //Выдан частично, с возвратом части товара (завершен)
    public const string RETURNED = 'returned'; //Полный возврат денег (завершен)

    public const array STATUSES = [
        self::NEW => 'Сформирован',
        self::DRAFT => 'В работе у менеджера',
        self::AWAITING => 'Ожидает оплаты',
        self::PREPAID => 'Внесена предоплата',
        self::PAID => 'Оплачен',
        self::SHIPPED => 'Выдан частично',

        self::COMPLETED => 'Завершен',
        self::COMPLETED_REFUND => 'Завершен с возвратом',
        self::CANCELLED => 'Отменен',
        self::RETURNED => 'Возврат оплаты',
    ];
    private const array ALLOWED = [
        self::NEW,
        self::DRAFT,
        self::AWAITING,
        self::PREPAID,
        self::PAID,
        self::SHIPPED,

        self::COMPLETED,
        self::COMPLETED_REFUND,
        self::CANCELLED,
        self::RETURNED,
    ];
    private string $value;



    public function __construct(
        string $value,
    )
    {
        $normalized = strtolower(trim($value));
        if (!in_array($normalized, self::ALLOWED, true)) {
            throw new InvalidArgumentException("Недопустимое значение пола: {$value}");
        }
        $this->value = $normalized;
    }

    public static function awaiting(): OrderStatus
    {
        return new self(OrderStatus::AWAITING);
    }

    public static function cancelled(): OrderStatus
    {
        return new self(OrderStatus::CANCELLED);
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value ?? '';
    }

    public static function new(): self
    {
        return new self(OrderStatus::NEW);
    }

    public static function draft(): self
    {
        return new self(OrderStatus::DRAFT);
    }
}
