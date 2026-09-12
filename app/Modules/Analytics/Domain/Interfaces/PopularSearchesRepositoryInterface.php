<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\Interfaces;

use App\Modules\Analytics\Domain\ReadModels\PopularSearchData;
use DateTimeImmutable;

interface PopularSearchesRepositoryInterface
{
    /**
     * Топ популярных запросов за период.
     *
     * @return PopularSearchData[]
     */
    public function getTop(string $periodType, DateTimeImmutable $periodDate, int $limit = 20): array;

    /** Пересоздать агрегат за период (вызывается кроном). */
    public function rebuildForPeriod(string $periodType, DateTimeImmutable $periodDate, DateTimeImmutable $from, DateTimeImmutable $to): void;

    /** Удалить старые агрегаты. */
    public function deleteOlderThan(DateTimeImmutable $before, string $periodType): int;

    /** Получить конкретный запрос. */
    public function findByQuery(string $queryNormalized, string $periodType, DateTimeImmutable $periodDate): ?PopularSearchData;
}
