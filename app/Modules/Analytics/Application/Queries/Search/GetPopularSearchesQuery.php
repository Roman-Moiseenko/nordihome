<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Queries\Search;

use App\Modules\Analytics\Domain\Interfaces\PopularSearchesRepositoryInterface;
use App\Modules\Analytics\Domain\ReadModels\PopularSearchData;
use DateTimeImmutable;

/**
 * GetPopularSearches — топ популярных поисковых запросов за период.
 */
final class GetPopularSearchesQuery
{
    public function __construct(
        private readonly PopularSearchesRepositoryInterface $popularSearches,
    ) {}

    /**
     * @return PopularSearchData[]
     */
    public function execute(string $periodType, DateTimeImmutable $periodDate, int $limit = 20): array
    {
        return $this->popularSearches->getTop($periodType, $periodDate, $limit);
    }
}
