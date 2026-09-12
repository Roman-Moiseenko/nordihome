<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\Interfaces;

use App\Modules\Analytics\Domain\ReadModels\PageDailyData;
use App\Modules\Analytics\Domain\ReadModels\PageMetricsData;
use DateTimeImmutable;

interface PageDailyRepositoryInterface
{
    /**
     * Топ страниц по типу за день.
     *
     * @return PageDailyData[]
     */
    public function getTopByType(DateTimeImmutable $date, string $pageType, int $limit = 20, string $sortBy = 'views_count'): array;

    /** Данные конкретной страницы за день. */
    public function findByEntity(DateTimeImmutable $date, string $pageType, int $entityId): ?PageDailyData;

    /** Пересоздать агрегат за день. */
    public function rebuildForDate(DateTimeImmutable $date, DateTimeImmutable $from, DateTimeImmutable $to): void;

    /** Средние метрики по типу страницы за период. */
    public function getAvgMetrics(string $pageType, DateTimeImmutable $from, DateTimeImmutable $to): ?PageMetricsData;

    /** Удалить старые агрегаты. */
    public function deleteOlderThan(DateTimeImmutable $before): int;
}
