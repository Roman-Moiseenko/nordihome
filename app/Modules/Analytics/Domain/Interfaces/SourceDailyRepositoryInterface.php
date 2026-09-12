<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\Interfaces;

use App\Modules\Analytics\Domain\Entities\SourceDailyEntity;
use DateTimeImmutable;

interface SourceDailyRepositoryInterface
{
    /**
     * Отчёт по источникам за период.
     *
     * @return SourceDailyEntity[]
     */
    public function getReport(DateTimeImmutable $from, DateTimeImmutable $to, ?string $source = null): array;

    /** Пересоздать агрегат за день. */
    public function rebuildForDate(DateTimeImmutable $date, DateTimeImmutable $from, DateTimeImmutable $to): void;

    /**
     * Топ источников за период.
     *
     * @return SourceDailyEntity[]
     */
    public function getTopSources(DateTimeImmutable $from, DateTimeImmutable $to, int $limit = 20): array;

    /**
     * Отчёт по UTM-кампаниям.
     *
     * @return SourceDailyEntity[]
     */
    public function getByCampaign(DateTimeImmutable $from, DateTimeImmutable $to, int $limit = 20): array;

    /** Удалить старые агрегаты. */
    public function deleteOlderThan(DateTimeImmutable $before): int;
}
