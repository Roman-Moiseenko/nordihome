<?php

declare(strict_types=1);

namespace App\Modules\Discount\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * Статус акции (Value Object).
 *
 * Возможные значения:
 *  - draft    — Черновик
 *  - waiting  — В ожидании
 *  - started  — Запущена
 *  - finished — Остановлена
 */
final class PromotionStatus
{
    public const string DRAFT = 'draft';
    public const string WAITING = 'waiting';
    public const string STARTED = 'started';
    public const string FINISHED = 'finished';

    private const array ALLOWED = [
        self::DRAFT,
        self::WAITING,
        self::STARTED,
        self::FINISHED,
    ];

    public const array STATUSES = [
        self::DRAFT => 'Черновик',
        self::WAITING => 'В ожидании',
        self::STARTED => 'Запущена',
        self::FINISHED => 'Остановлена',
    ];

    public function __construct(private readonly string $value)
    {
        if (!in_array($value, self::ALLOWED, true)) {
            throw new InvalidArgumentException('Неизвестный статус акции: ' . $value);
        }
    }

    public static function draft(): self
    {
        return new self(self::DRAFT);
    }

    public static function waiting(): self
    {
        return new self(self::WAITING);
    }

    public static function started(): self
    {
        return new self(self::STARTED);
    }

    public static function finished(): self
    {
        return new self(self::FINISHED);
    }

    /**
     * Статус по умолчанию (для новых акций, у которых ещё нет статуса).
     */
    public static function default(): self
    {
        return self::draft();
    }

    public function value(): string
    {
        return $this->value;
    }

    public function label(): string
    {
        return self::STATUSES[$this->value];
    }

    public function isDraft(): bool
    {
        return $this->value === self::DRAFT;
    }

    public function isWaiting(): bool
    {
        return $this->value === self::WAITING;
    }

    public function isStarted(): bool
    {
        return $this->value === self::STARTED;
    }

    public function isFinished(): bool
    {
        return $this->value === self::FINISHED;
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
