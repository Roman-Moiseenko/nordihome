<?php

namespace App\Modules\Lead\Domain\ValueObjects;

use InvalidArgumentException;

final class LeadStatusValue
{
    const string NEW_LEAD = 'new_lead';
    const string IN_WORK = 'in_work';
    const string NOT_DECIDED = 'not_decide';
    const string INVOICE = 'invoice';
    const string PAID = 'paid';
    const string ASSEMBLY = 'assembly';
    const string DELIVERY = 'delivery';
    const string CANCELED = 'canceled';
    const string COMPLETED = 'completed';

    const array STATUSES = [
        self::NEW_LEAD => 'Новый',
        self::IN_WORK => 'В работе',
        self::NOT_DECIDED => 'Клиент думает',
        self::INVOICE => 'Выставлен счет',
        self::PAID => 'Оплачен',
        self::ASSEMBLY => 'На сборке',
        self::DELIVERY => 'На доставке',
        self::CANCELED => 'Отменен',
        self::COMPLETED => 'Завершен',
    ];

    public function __construct(private readonly string $value)
    {
        if (!array_key_exists($value, self::STATUSES)) {
            throw new InvalidArgumentException("Недопустимый статус лида: {$value}");
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getName(): string
    {
        return self::STATUSES[$this->value];
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
