<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\DTOs\Analytics;

use DateTimeImmutable;
use Spatie\LaravelData\Attributes\Nullable;
use Spatie\LaravelData\Attributes\StringType;
use Spatie\LaravelData\Data;

/**
 * AnalyticsFilterData — фильтр периода для отчётов аналитики.
 *
 * Используется только для чтения (передача параметров из query-string),
 * поэтому строгой валидации нет — поля nullable со значениями по умолчанию.
 */
class AnalyticsFilterData extends Data
{
    public function __construct(
        #[Nullable, StringType]
        public readonly ?string $dateFrom = null,
        #[Nullable, StringType]
        public readonly ?string $dateTo = null,
    ) {
    }

    /**
     * Начало периода. По умолчанию — 30 дней назад.
     */
    public function getFrom(): DateTimeImmutable
    {
        if (!empty($this->dateFrom)) {
            return new DateTimeImmutable($this->dateFrom . ' 00:00:00');
        }

        return (new DateTimeImmutable('now'))->modify('-30 days')->setTime(0, 0);
    }

    /**
     * Конец периода. По умолчанию — сегодня, конец дня.
     */
    public function getTo(): DateTimeImmutable
    {
        if (!empty($this->dateTo)) {
            return new DateTimeImmutable($this->dateTo . ' 23:59:59');
        }

        return (new DateTimeImmutable('now'))->setTime(23, 59, 59);
    }
}
