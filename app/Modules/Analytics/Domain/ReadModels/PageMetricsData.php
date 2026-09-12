<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\ReadModels;

/**
 * PageMetricsData — сводные (средние) метрики по типу страницы за период.
 */
final class PageMetricsData
{
    public function __construct(
        public readonly string $pageType,
        public readonly int $viewsCount,
        public readonly int $uniqueVisitorsCount,
        public readonly ?int $avgDuration,
        public readonly int $bounceCount,
    ) {
    }
}
