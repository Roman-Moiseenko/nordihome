<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\ReadModels;

/**
 * PopularSearchData — read-модель агрегата популярных поисковых запросов.
 */
final class PopularSearchData
{
    public function __construct(
        public readonly string $queryNormalized,
        public readonly string $querySample,
        public readonly int $searchesCount,
        public readonly int $uniqueVisitorsCount,
        public readonly int $clicksCount,
        public readonly string $periodDate,
        public readonly string $periodType,
    ) {
    }
}
