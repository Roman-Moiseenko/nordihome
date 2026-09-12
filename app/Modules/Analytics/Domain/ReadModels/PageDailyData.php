<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\ReadModels;

/**
 * PageDailyData — read-модель дневного агрегата по странице.
 */
final class PageDailyData
{
    public function __construct(
        public readonly string $date,
        public readonly string $pageType,
        public readonly ?int $entityId,
        public readonly int $viewsCount,
        public readonly int $uniqueVisitorsCount,
        public readonly ?int $avgDuration,
        public readonly int $bounceCount,
    ) {
    }
}
